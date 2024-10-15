<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {variable} compiler function plugin
 *
 * Type:     compiler function<br>
 * Name:     variable<br>
 * Purpose:  display a value from a template variable
  * @param string containing var-attribute and value-attribute
 * @param Smarty_Compiler
 */
function smarty_compiler_variable($tag_attrs, &$compiler)
{
    $_params = $compiler->_parse_attrs($tag_attrs);

    if (!isset($_params['name'])) {
        $compiler->_syntax_error("variable: missing 'name' parameter", E_USER_WARNING);
        return;
    }

    if (isset($_params['assign'])) {
        return "\$this->assign({$_params['assign']}, \$this->get_template_vars({$_params['name']}));";
    }

    return "echo \$this->get_template_vars({$_params['name']});";
}

/* vim: set expandtab: */
