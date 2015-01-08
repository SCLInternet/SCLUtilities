<?php

namespace SCL\HTML;

class AccordionBuilder extends SimpleTableBuilder
{
    public function build($title, array $rows)
    {
        $convertRows = $this->convertRows($rows);
        if (count($convertRows) == 0) {
            parent::build($title, $rows);

            return;
        }
        $this->accordionStart($title);
        $this->accordionBody($convertRows);
        $this->accordionEnd();
    }

    /** @param string $title */
    private function accordionStart($title)
    {
        $this->builder->pushTag('div', ['class' => 'panel panel-default']);
        $this->builder->inlineTag('div', ['class' => 'panel-heading'], $title);
        $this->builder->pushTag('div', ['class' => 'panel-body']);
        $this->builder->pushTag('div', ['id' => 'accordion', 'title' => $title, 'class' => 'bm-accordion']);
    }

    private function accordionBody(array $rows)
    {
        $counter = 1;
        foreach ($rows as $name => $row) {
            $this->accordionRow($row, $name, $counter++);
        }
    }

    private function accordionEnd()
    {
        $this->builder->popTag('div');
        $this->builder->popTag('div');
        $this->builder->popTag('div');
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    private function convertRows(array $rows)
    {
        $newRows = [];
        foreach ($rows as $row) {
            $temp = [];
            $name = null;
            foreach ($row as $key => $value) {
                if ($key == 'name') {
                    $name = $value;
                } else {
                    $temp[] = ['name' => $key, 'value' => $value];
                }
            }
            if ($name) {
                $newRows[$name] = $temp;
            }
        }

        return $newRows;
    }

    /**
     * @param string $id
     * @param string $name
     */
    private function buildCollapsibleSection($id, $name)
    {
        $this->builder->pushTag('div', ['class' => 'panel-heading']);
        $this->builder->pushTag('h4', ['class' => 'panel-title']);
        $this->builder->inlineTag(
            'a',
            [
                'data-toggle' => 'collapse',
                'data-parent' => '#accordion',
                'href'        => "#$id"
            ],
            $name
        );
        $this->builder->popTag('h4');
        $this->builder->popTag('div');
    }

    /**
     * @param string $name
     * @param int $counter
     */
    private function accordionRow(array $row, $name, $counter)
    {
        $id = "collapse$counter";
        $this->builder->pushTag('div', ['class' => 'panel panel-default']);
        $this->buildCollapsibleSection($id, $name);
        $this->builder->pushTag('div', ['id' => $id, 'class' => 'panel-collapse collapse']);
        $this->builder->pushTag('div', ['class' => 'panel-body']);
        if (count($row)) {
            $this->buildInnerTable('', $row);
        }
        $this->builder->popTag('div');
        $this->builder->popTag('div');
        $this->builder->popTag('div');
    }
}
