<?php
require dirname(__DIR__, 3) . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsmodule.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

global $xoopsUser; $xoopsDB;
$myts = MyTextSanitizer::getInstance();
$op = '';

if (isset($_GET)) {
    foreach ($_GET as $k => $v) {
        ${$k} = $v;
    }
}
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}
switch ($op) {
case 'list':
    xoops_cp_header();

    require __DIR__ . '/addform.php';
    echo _MD_A_HINTO . '<br><br>
	<center><table class=outer cellSpacing=1><tr>
	<th>' . _MD_A_MODULES . '</th>
	<th>' . _MD_A_THEMES . '</th>
	<th>' . _MD_A_SITE_NAME . '</th>
	<th>' . _MD_A_TITTLE . '</th>
	<th>' . _MD_A_META_KEYWORDS . '</th>
	<th>' . _MD_A_META_DESC . '</th>
	<th width="40">&nbsp;</th></tr>';
    $i = 0;
    $result = $xoopsDB->query('SELECT c.mid, c.theme, c.title, c.subtitle, c.metakey, c.metadesc, m.name FROM ' . $xoopsDB->prefix('ctem_link') . ' c, ' . $xoopsDB->prefix('modules') . ' m WHERE c.mid=m.mid');
        while (list($mid, $theme, $title, $subtitle, $metakey, $metadesc, $name) = $xoopsDB->fetchRow($result)) {
            if (0 == $i % 2) {
                $class = 'even';
            } else {
                $class = 'odd';
            }

            $i++;

            echo '<tr class="' . $class . '">
		<td>' . $name . '</td>
		<td>' . $theme . '</td>
		<td>' . $title . '</td>
		<td>' . $subtitle . '</td>
		<td>' . $metakey . '</td>
		<td>' . $metadesc . '</td>
		<td>
		<a href="index.php?op=alt&mid=' . $mid . '">' . _EDIT . '</a>
		<br>
		<a href="index.php?op=del&mid=' . $mid . '">' . _DELETE . '</a>
		</td>
		</tr>';
        }

    echo '<tr>
	<th>' . _MD_A_PAGE . '</th>
	<th>' . _MD_A_THEMES . '</th>
	<th>' . _MD_A_SITE_NAME . '</th>
	<th>' . _MD_A_TITTLE . '</th>
	<th>' . _MD_A_META_KEYWORDS . '</th>
	<th>' . _MD_A_META_DESC . '</th>
	<th width="40">&nbsp;</th></tr>';
    $i = 0;
    $result = $xoopsDB->query('SELECT pagename, theme, title, subtitle, metakey, metadesc FROM ' . $xoopsDB->prefix('ctem_pagelink') . ' WHERE pid=1');
     while (list($pagename, $theme, $title, $subtitle, $metakey, $metadesc) = $xoopsDB->fetchRow($result)) {
         if (0 == $i % 2) {
             $class = 'even';
         } else {
             $class = 'odd';
         }

         $i++;

         echo '  <tr class="' . $class . '">
		<td>' . $pagename . '</td>
		<td>' . $theme . '</td>
		<td>' . $title . '</td>
		<td>' . $subtitle . '</td>
		<td>' . $metakey . '</td>
		<td>' . $metadesc . '</td>
		<td>
		<a href="index.php?op=pagealt&pid=1">' . _EDIT . '</a>
		</td></tr>';
     }
    $result = $xoopsDB->query('SELECT pagename, theme, title, subtitle, metakey, metadesc FROM ' . $xoopsDB->prefix('ctem_pagelink') . ' WHERE pid=0');
    [$pagename, $theme, $title, $subtitle, $metakey, $metadesc] = $xoopsDB->fetchRow($result);
    echo '  <tr>
		<td>' . $pagename . '<br>(' . _MD_A_OTHERPAGES . ')</td>
		<td>' . $theme . '</td>
		<td>' . $title . '</td>
		<td>' . $subtitle . '</td>
		<td>' . $metakey . '</td>
		<td>' . $metadesc . '</td>
		<td>
		<a href="index.php?op=pagealt&pid=0">' . _EDIT . '</a>
		</td></tr>';

    echo '</table></center>';

    xoops_cp_footer();
    break;
case 'add':
    $stop = '';
    $sql = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('ctem_link') . " WHERE mid='" . $mid . "'";
    $result = $xoopsDB->query($sql);
    [$count] = $xoopsDB->fetchRow($result);
    if ($count > 0) {
        $stop .= _MD_A_ALREADY_REGISTERED;
    }
    $sql = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('modules') . " WHERE mid='" . $mid . "'";
    $result = $xoopsDB->query($sql);
    [$count] = $xoopsDB->fetchRow($result);
    if (0 == $count) {
        $stop .= _MD_A_THIS_MODULE_NOTFOUND;
    }
    if (!file_exists(XOOPS_ROOT_PATH . '/themes/' . $theme . '/theme.html')) {
        $stop .= _MD_A_THIS_THEME_NOTFOUND;
    }

    if (empty($stop)) {
        $sql = 'INSERT INTO ' . $xoopsDB->prefix('ctem_link') . ' (mid, theme, title, subtitle, metakey, metadesc) VALUES(' . $mid . ", '" . $theme . "', '" . $title . "', '" . $subtitle . "', '" . $metakey . "', '" . $metadesc . "')";

        $xoopsDB->fetchRow($xoopsDB->queryF($sql));

        redirect_header('index.php?op=list', 2, _MD_A_REGSTERED);

        exit();
    }  
        xoops_cp_header();
        echo $stop;
        require __DIR__ . '/addform.php';
        xoops_cp_footer();

    break;
case 'alt':
    $result = $xoopsDB->query('SELECT theme, title, subtitle, metakey, metadesc FROM ' . $xoopsDB->prefix('ctem_link') . ' WHERE mid=' . $mid);
    [$theme, $title, $subtitle, $metakey, $metadesc] = $xoopsDB->fetchRow($result);
    if (empty($theme)) {
        redirect_header('index.php?op=list', 5, _MD_A_THIS_PAIR_NOTFOUND);

        exit();
    }
    xoops_cp_header();
    $form = new XoopsThemeForm(_MD_A_EDIT_PAIR_OF_THEME_MODULE, 'form', 'index.php');
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
    $form->addElement(new XoopsFormHidden('op', 'updata'));
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $form->display();
    xoops_cp_footer();

    break;
case 'pagealt':
    $result = $xoopsDB->query('SELECT pagename, theme, title, subtitle, metakey, metadesc FROM ' . $xoopsDB->prefix('ctem_pagelink') . ' WHERE pid=' . $pid);
    [$pagename, $theme, $title, $subtitle, $metakey, $metadesc] = $xoopsDB->fetchRow($result);
    if (empty($theme)) {
        redirect_header('index.php?op=list', 5, _MD_A_THIS_PAIR_NOTFOUND);

        exit();
    }
    xoops_cp_header();
    $form = new XoopsThemeForm(_MD_A_EDIT_PAIR_OF_THEME_PAGE, 'form', 'index.php');
    $form->addElement(new XoopsFormHidden('pid', $pid));
    $form->addElement(new XoopsFormLabel(_MD_A_PAGE, $pagename));
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
    $form->addElement(new XoopsFormHidden('op', 'pageupdata'));
    $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $form->display();
    xoops_cp_footer();

    break;
case 'updata':
    $stop = '';
    $sql = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('ctem_link') . " WHERE mid='" . $mid . "'";
    $result = $xoopsDB->query($sql);
    [$count] = $xoopsDB->fetchRow($result);
    if (0 == $count) {
        $stop .= _MD_A_THIS_PAIR_NOTFOUND;
    }
    if (!file_exists(XOOPS_ROOT_PATH . '/themes/' . $theme . '/theme.html')) {
        $stop .= _MD_A_THIS_THEME_NOTFOUND;
    }

    if (empty($stop)) {
        $sql = 'UPDATE ' . $xoopsDB->prefix('ctem_link') . " SET theme = '" . $theme . "', title = '" . $title . "', subtitle = '" . $subtitle . "', metakey = '" . $metakey . "', metadesc = '" . $metadesc . "' WHERE mid = " . $mid;

        $xoopsDB->fetchRow($xoopsDB->queryF($sql));

        redirect_header('index.php?op=list', 2, _MD_A_RENEWED);

        exit();
    }  
        echo $stop;

    break;
case 'pageupdata':
    $stop = '';
    $sql = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('ctem_pagelink') . " WHERE pid='" . $pid . "'";
    $result = $xoopsDB->query($sql);
    [$count] = $xoopsDB->fetchRow($result);
    if (0 == $count) {
        $stop .= _MD_A_THIS_PAIR_NOTFOUND;
    }
    if (!file_exists(XOOPS_ROOT_PATH . '/themes/' . $theme . '/theme.html')) {
        $stop .= _MD_A_THIS_THEME_NOTFOUND;
    }

    if (empty($stop)) {
        $sql = 'UPDATE ' . $xoopsDB->prefix('ctem_pagelink') . " SET theme = '" . $theme . "', title = '" . $title . "', subtitle = '" . $subtitle . "', metakey = '" . $metakey . "', metadesc = '" . $metadesc . "' WHERE pid = " . $pid;

        $xoopsDB->fetchRow($xoopsDB->queryF($sql));

        redirect_header('index.php?op=list', 2, _MD_A_RENEWED);

        exit();
    }  
        echo $stop;

    break;
case 'del':
    xoops_cp_header();
    xoops_confirm(['op' => 'delete', 'mid' => $mid], 'index.php', _MD_A_REALLY_DEL);
    xoops_cp_footer();
    break;
case 'delete':
    if (0 == $mid) {
        redirect_header('index.php?op=list', 2, _MD_A_COULDNT_DEL);

        exit();
    }  
        $sql = 'DELETE FROM ' . $xoopsDB->prefix('ctem_link') . ' WHERE mid = ' . $mid;
        $xoopsDB->fetchRow($xoopsDB->queryF($sql));

        redirect_header('index.php?op=list', 2, _MD_A_DELETED);
        exit();

    break;
default:
    xoops_cp_header();
    ?>
	<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class="odd">
	<a href='./index.php'><h4><?php echo _MD_A_CTEM; ?></h4></a>

	<table border="0" cellpadding="4" cellspacing="1" width="100%">
	<tr class='bg1' align="left">
	<td><span class='fg2'><a href="index.php?op=list"><?php echo _MD_A_SETEDITDEL; ?></a></span></td>
	<td><span class='fg2'><?php echo _MD_A_SETEDITDELDESC; ?></span></td>
	</tr>
	</table>
	</td></tr></table>
	<?php
    xoops_cp_footer();
    break;
}
?>
