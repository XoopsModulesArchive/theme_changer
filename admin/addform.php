<?php

    $mid = $mid ?? '';
    $theme = $theme ?? '';
    $title = $title ?? '';
    $subtitle = $subtitle ?? '';
    $metakey = $metakey ?? '';
    $metadesc = $metadesc ?? '';
    $form = new XoopsThemeForm(_MD_A_NEW_PAIR_OF_THEME_MODULE, 'form', 'index.php');
    $module_radio = new XoopsFormSelect(_MD_A_MODULES, 'mid', $mid);
        $moduleHandler = xoops_getHandler('module');
        $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
        $criteria->add(new Criteria('isactive', 1));
        $module_list = $moduleHandler->getList($criteria);
        ksort($module_list);
    $module_radio->addOptionArray($module_list);
    $form->addElement($module_radio);
    $dirname = XOOPS_THEME_PATH . '/';
    $dirlist = [];
        if (is_dir($dirname) && $handle = opendir($dirname)) {
            while (false !== ($file = readdir($handle))) {
                if (!preg_match("/^[\.]{1,2}$/", $file)) {
                    if ('cvs' != mb_strtolower($file) && is_dir($dirname . $file) && 'z_changeable_theme' != $file) {
                        $dirlist[$file] = $file;
                    }
                }
            }

            closedir($handle);

            asort($dirlist);

            reset($dirlist);
        }
    $theme_select = new XoopsFormSelect(_MD_A_THEMES, 'theme', $theme);
    $theme_select->addOptionArray($dirlist);
    $form->addElement($theme_select);
    $form->addElement(new XoopsFormText(_MD_A_SITE_NAME, 'title', 50, 50, $title));
    $form->addElement(new XoopsFormText(_MD_A_TITTLE, 'subtitle', 50, 50, $subtitle));
    $form->addElement(new XoopsFormTextArea(_MD_A_META_KEYWORDS, 'metakey', $metakey));
    $form->addElement(new XoopsFormTextArea(_MD_A_META_DESC, 'metadesc', $metadesc));
    $form->addElement(new XoopsFormHidden('op', 'add'));
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $form->display();
