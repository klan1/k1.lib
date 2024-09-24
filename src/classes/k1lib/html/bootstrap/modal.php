<?php

namespace k1lib\html\bootstrap;

use k1lib\html\div;

class modal extends div {

    use bootstrap_methods;

    protected $row_number = 0;

    /**
     * @param integer $col_number
     * @param integer $class
     * @param integer $id
     *  */
    public function __construct($modal_title, $content, $cancel = 'Cancelar', $ok = 'OK') {
        parent::__construct('modal fade vh-100', 'staticBackdrop');
        $this->set_attrib('data-bs-backdrop', 'static');
        $this->set_attrib('data-bs-keyboard', 'false');
        $this->set_attrib('tabindex', '-1');
        $this->set_attrib('aria-labelledby', 'staticBackdropLabel');
        $this->set_attrib('aria-hidden', 'true');

        $modal_body = (new div('modal-body'))->set_value($content);

        $btn_cancel = $cancel ? "<button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">{$cancel}</button>" : '';
        $btn_ok = $ok ? "<button type=\"button\" class=\"btn btn-primary\">{$ok}</button>" : '';
        $html_modal = <<<HTML
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">{$modal_title}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                {$modal_body}
                  <div class="modal-footer">
                    {$btn_cancel}
                    {$btn_ok}
                  </div>
                </div>
            </div>
HTML;
        $this->set_value($html_modal);
        $this->link_value_obj($modal_body);
    }
}
