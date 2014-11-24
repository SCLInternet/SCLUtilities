<?php

namespace spec\SCL\HTML;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SCL\HTML\HTMLBuilder;
use SCL\HTML\NamePrettifier;
use SCL\PHPSpec\Matchers;

class SimpleTableBuilderSpec extends ObjectBehavior
{
    /** @var  HTMLBuilder */
    private $HTMLBuilder;

    public function let()
    {
        $this->HTMLBuilder = new HTMLBuilder();
        $this->beConstructedWith($this->HTMLBuilder, new FieldPrettifier());
    }

    public function it_constructs()
    {
        $this->shouldHaveType('SCL\HTML\SimpleTableBuilder');
    }

    public function it_builds_empty_table()
    {
        $this->buildText("title2", [])->shouldContain(
            [
                '<table class="table table-hover table-condensed" title="Title 2">',
                '</table>'
            ]
        );
    }

    public function it_builds_a_single_column_table()
    {
        $this->buildText("Title", [["Greetings" => "Hello"]])->shouldContain(
            [
                '<table class="table table-hover table-condensed" title="Title">',
                '   <tr>',
                '      <th>Greetings</th>',
                '   </tr>',
                '   <tr>',
                '      <td>Hello</td>',
                '   </tr>',
                '</table>'
            ]
        );
    }

    public function it_builds_a_different_single_column_table()
    {
        $this->buildText("Title", [["Salutations" => "Bore da"]])->shouldContain(
            [
                '<table class="table table-hover table-condensed" title="Title">',
                '   <tr>',
                '      <th>Salutations</th>',
                '   </tr>',
                '   <tr>',
                '      <td>Bore da</td>',
                '   </tr>',
                '</table>'
            ]
        );
    }

    public function it_builds_a_headerless_table_for_name_value_pairs()
    {
        $this->buildText('Title', [['name' => 'unitName', 'value' => 'Carafan Teifi']])
            ->shouldContain(
                [
                    '<table class="table table-hover table-condensed" title="Title">',
                    '<tr>',
                    '<td>Unit name</td>',
                    '<td>Carafan Teifi</td>',
                    '</tr>',
                    '</table>'
                ]
            );
    }

    public function it_builds_a_single_column_table_with_an_alias()
    {
        $this->beConstructedWith(new HTMLBuilder(), new FieldPrettifier());
        $this->buildText("Title", [["honorific" => "Bore da"]])->shouldContain(
            [
                '<table class="table table-hover table-condensed" title="Title">',
                '   <tr>',
                '      <th>Title</th>',
                '   </tr>',
                '   <tr>',
                '      <td>Bore da</td>',
                '   </tr>',
                '</table>'
            ]
        );
    }

    public function it_builds_a_single_column_table_with_reformatted_id()
    {
        $this->beConstructedWith(new HTMLBuilder(), new FieldPrettifier());
        $this->buildText("Title", [["unitName" => "Carafan Teifi"]])->shouldContain(
            [
                '<table class="table table-hover table-condensed" title="Title">',
                '   <tr>',
                '      <th>Unit name</th>',
                '   </tr>',
                '   <tr>',
                '      <td>Carafan Teifi</td>',
                '   </tr>',
                '</table>'
            ]
        );
    }

    public function it_builds_a_single_column_table_with_multiple_rows()
    {
        $this->buildText("Title", [["Greetings" => "Hello"], ["Greetings" => "Bore da"]])->shouldContain(
            [
                '<table class="table table-hover table-condensed" title="Title">',
                '   <tr>',
                '      <th>Greetings</th>',
                '   </tr>',
                '   <tr>',
                '      <td>Hello</td>',
                '   </tr>',
                '   <tr>',
                '      <td>Bore da</td>',
                '   </tr>',
                '</table>'
            ]
        );
    }

    public function it_builds_a_double_column_table_with_multiple_rows()
    {
        $this->buildText(
            "Title",
            [
                ["Greetings" => "Hello", "Name" => "Steff"],
                ["Greetings" => "Bore da", "Name" => "Rob"]
            ]
        )->shouldContain(
            [
                '<table class="table table-hover table-condensed" title="Title">',
                '   <tr>',
                '      <th>Greetings</th>',
                '      <th>Name</th>',
                '   </tr>',
                '   <tr>',
                '      <td>Hello</td>',
                '      <td>Steff</td>',
                '   </tr>',
                '   <tr>',
                '      <td>Bore da</td>',
                '      <td>Rob</td>',
                '   </tr>',
                '</table>'
            ]
        );
    }

    public function getMatchers()
    {
        return [
            'returnLines' => Matchers::returnLinesMatcher(),
            'contain'     => Matchers::containsMatcher(),
        ];
    }
}

class FieldPrettifier extends NamePrettifier
{
    public function __construct()
    {
        parent::__construct([
            'line1'     => 'address1',
            'line2'     => 'address2',
            'honorific' => 'title',
            'seasonId'  => 'pickSeason'
        ]);
    }
}
