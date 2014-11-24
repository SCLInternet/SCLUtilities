<?php
namespace SCL\HTML;

class SimpleTableBuilder
{
    /** @var HTMLBuilder */
    protected $builder;

    /** @var NamePrettifier */
    protected $prettifier;

    /** @var CustomDataPresenter */
    private $presenter;

    public function __construct(
        HTMLBuilder $htmlBuilder,
        NamePrettifier $prettifier,
        CustomDataPresenter $presenter = null
    ) {
        $this->builder    = $htmlBuilder;
        $this->prettifier = $prettifier;
        $this->setPresenter($presenter);
    }

    public function setPresenter(CustomDataPresenter $presenter = null)
    {
        $this->presenter = $presenter;
    }

    public function buildText($title, array $rows)
    {
        $this->build($title, $rows);

        return $this->builder->getText();
    }

    public function build($title, array $rows)
    {
        $this->tableStart($title);
        $this->body($rows);
        $this->tableEnd();
    }

    /** @param string $title */
    protected function tableStart($title)
    {
        $title = $this->prettifier->prettifyLabelName($title);
        $this->builder->panelStart($title);
        $this->builder->pushTag(
            'table',
            [
                'class' => "table table-hover table-condensed",
                'title' => $title
            ]
        );
    }

    protected function body(array $rows)
    {
        if (!count($rows)) {
            return;
        }
        if ($this->isRecordTable($rows)) {
            $this->buildRecordTable($rows);
        } else {
            $this->buildRowsTable($rows);
        }
    }

    /**
     * @param array $rows
     *
     * @return bool
     */
    public function isRecordTable(array $rows)
    {
        $headings = $this->getColumnHeadings($rows);
        if ($headings == ['name', 'value']) {
            return true;
        }

        return false;
    }

    /** @return array */
    protected function getColumnHeadings(array $rows)
    {
        $headings = [];
        if (!$rows || count($rows) == 0) {
            return $headings;
        }
        $row = $rows[0];
        foreach ($row as $name => $value) {
            if (is_numeric($name)) {
                break;
            }
            $headings[] = $name;
        }

        return $headings;
    }

    protected function buildRecordTable($rows)
    {
        foreach ($rows as $row) {
            $this->buildRecordRow($row);
        }
    }

    protected function buildRecordRow(array $row)
    {
        $name  = $row['name'];
        $value = $row['value'];
        $this->builder->pushTag('tr', []);
        $this->builder->inlineTag('td', [], $this->getHeadingName($name));
        $this->buildValue($value, $name);
        $this->builder->popTag('tr');
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getHeadingName($name)
    {
        return htmlentities($this->prettifier->prettifyLabelName(lcfirst($name)));
    }

    /**
     * @param mixed  $value
     * @param string $name
     */
    protected function buildValue($value, $name)
    {
        if (!is_null($this->presenter) && $this->presenter->transformData($value, $name)) {
            return;
        }
        $this->builder->inlineTag('td', [], $this->textOf($value, $name));
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function textOf($value)
    {
        if (is_bool($value)) {
            $value = $value ? 'Yes' : 'No';
        }

        return htmlentities($value);
    }

    /**
     * @param array $rows
     */
    protected function buildRowsTable(array $rows)
    {
        $this->buildHeading($rows);
        $this->buildData($rows);
    }

    protected function buildHeading(array $rows)
    {
        if ($this->isRecordTable($rows)) {
            return;
        }
        $headings = $this->getColumnHeadings($rows);
        $this->builder->pushTag('tr', []);
        foreach ($headings as $name) {
            $this->builder->inlineTag("th", [], $this->getHeadingName($name));
        }
        $this->builder->popTag('tr');
    }

    protected function buildData(array $rows)
    {
        foreach ($rows as $row) {
            $this->buildRow($row);
        }
    }

    protected function buildRow(array $row)
    {
        $this->builder->pushTag('tr', []);
        foreach ($row as $name => $value) {
            $this->buildValue($value, $name);
        }
        $this->builder->popTag('tr');
    }

    protected function tableEnd()
    {
        $this->builder->popTag('table');
        $this->builder->panelEnd();
    }

    /**
     * @param string $title
     * @param array $rows
     */
    public function buildInnerTable($title, array $rows)
    {
        $this->innerTableStart($title);
        $this->body($rows);
        $this->innerTableEnd();
    }

    /** @param string $title */
    protected function innerTableStart($title)
    {
        $title = $this->prettifier->prettifyLabelName($title);
        $this->builder->pushTag(
            'table',
            [
                'class' => "table table-condensed",
                'title' => $title
            ]
        );
    }

    protected function innerTableEnd()
    {
        $this->builder->popTag('table');
    }
}
