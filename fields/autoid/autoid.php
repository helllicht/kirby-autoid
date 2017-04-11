<?php

class AutoidField extends BaseField {

  public function input() {

    $input = new Brick('input', null);
    $input->addClass('input');
    $input->attr(array(
      'type'         => 'text',
      'value'        => $this->value(),
      'name'         => 'autoid',
      'placeholder'  => '–',
      'readonly'     => true,
      'id'           => $this->id(),
      'tabindex'     => '-1'
    ));

    $input->addClass('input-is-readonly');

    return $input;

  }

}
