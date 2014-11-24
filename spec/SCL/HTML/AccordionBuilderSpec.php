<?php
namespace spec\SCL\HTML;

use SCL\HTML\NamePrettifier;
use SCL\PHPSpec\Matchers;
use PhpSpec\ObjectBehavior;
use SCL\HTML\HTMLBuilder;

class AccordionBuilderSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(new HTMLBuilder(), new ABFieldPrettifier());
    }

    public function it_constructs()
    {
        $this->shouldHaveType('SCL\HTML\AccordionBuilder');
        $this->shouldHaveType('SCL\HTML\SimpleTableBuilder');
    }

    public function it_builds_an_empty_accordion()
    {
        $this->buildText('', [])->shouldContain(
            [
                '<table class="table table-hover table-condensed" title="">',
                '</table>'
            ]
        );
    }

    public function it_builds_an_single_row_accordion()
    {
        $this->buildText('Title', [["Greetings" => "hello", 'name' => 'Rob']])->shouldContain(
            [
                '<div class="panel panel-default">',
                '  <div class="panel-heading">Title</div>',
                '      <div class="panel-body">',
                '         <div id="accordion" title="Title" class="bm-accordion">',
                '             <div class="panel panel-default">',
                '                 <div class="panel-heading">',
                '                     <h4 class="panel-title">',
                '                         <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Rob</a>',
                '                     </h4>',
                '                 </div>',
                '                 <div id="collapse1" class="panel-collapse collapse">',
                '                     <div class="panel-body">',
                '                         <table class="table table-condensed" title="">',
                '                             <tr>',
                '                                 <td>Greetings</td>',
                '                                 <td>hello</td>',
                '                             </tr>',
                '                         </table>',
                '                     </div>',
                '                 </div>',
                '             </div>',
                '          </div>',
                '    </div>',
                '</div>',
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

class ABFieldPrettifier extends NamePrettifier
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
