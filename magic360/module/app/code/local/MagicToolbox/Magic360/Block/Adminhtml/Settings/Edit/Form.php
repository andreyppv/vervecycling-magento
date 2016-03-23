<?php

class MagicToolbox_Magic360_Block_Adminhtml_Settings_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm()  {

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            'class' => 'magic360EditForm'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();

    }

    protected function _afterToHtml($html) {

        $html .= '
<script type="text/javascript">

    getElementsByClass = function(classList, node) {
        var node = node || document;
        if(node.getElementsByClassName) {
            return node.getElementsByClassName(classList);
        } else {
            var nodes = node.getElementsByTagName("*"),
            nodesLength = nodes.length,
            classes = classList.split(/\s+/),
            classesLength = classes.length,
            result = [], i,j;
            for(i = 0; i < nodesLength; i++) {
                for(j = 0; j < classesLength; j++)  {
                    if(nodes[i].className.search("\\\\b" + classes[j] + "\\\\b") != -1) {
                        result.push(nodes[i]);
                        break;
                    }
                }
            }
            return result;
        }
    }

    var fieldsets = getElementsByClass("magic360Fieldset");
    var header = null;
    var buttons = null;
    var magic360FieldsetId = "";
    for(var i = 0, l = fieldsets.length; i < l; i++) {
        header = fieldsets[i].previousSibling;
        while(header.nodeType!=1) {
            header = header.previousSibling;
        }
        header.style.cursor = "pointer";
        buttons = getElementsByClass("form-buttons", header);
        buttons[0].innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        buttons[0].className += " fieldsetOpen";
        header.onclick = function() {
            var buttons = getElementsByClass("form-buttons", this);
            var fieldset = this.nextSibling;
            while(fieldset.nodeType!=1) {
                fieldset = fieldset.nextSibling;
            }
            if(buttons[0].className.match(/\bfieldsetOpen\b/)) {
                buttons[0].className = buttons[0].className.replace(/\bfieldsetOpen\b/, "fieldsetClose");
                fieldset.style.display = "none";
                this.style.marginBottom = "5px";
            } else {
                buttons[0].className = buttons[0].className.replace(/\bfieldsetClose\b/, "fieldsetOpen");
                fieldset.style.display = "block";
                this.style.marginBottom = "0px";
            }
            return false;
        }
        var id = fieldsets[i].id.replace(/_group_fieldset_\d+/g, "");
        if(magic360FieldsetId != id) {
            magic360FieldsetId = id;
        } else {
            header.click();
        }
    }
</script>
';

        return parent::_afterToHtml($html);

    }

}
