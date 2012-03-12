<?php
# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, 		
	'Default Content',
	'1.2',
	'Mike Henken',	
	'http://www.michaelhenken.com/',  
	'Fills in content area in create new page screen.', 
	'pages', 	
	'default_content'  	
);

# activate hooks
add_action('pages-sidebar','createSideMenu',array($thisfile,'Default Content'));
add_action('edit-extras','show_default_content');
define('DCFile', GSDATAOTHERPATH  . 'defaultContent.xml');

global $error_cate;
$error_cate = '';

global $EDLANG, $EDOPTIONS, $toolbar, $EDTOOL;
if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {	$EDLANG = 'en'; }
if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else {	$EDTOOL = 'basic'; }
if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {	$EDOPTIONS = ''; }
if ($EDTOOL == 'advanced') {
$toolbar = "
	    ['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
    '/',
    ['Styles','Format','Font','FontSize']
";
} elseif ($EDTOOL == 'basic') {
$toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
} else {
$toolbar = GSEDITORTOOL;
}

class DefaultContent 
{
	public function __construct()
	{
		//Check If File Is Created. Create If It is not
		if(!file_exists(DCFile))
		{
			$this->processDefaultContent();
		}
	}
	
	public function getDefaultContent()
	{
		$data_file = getXML(DCFile);
		return $data_file->defaultcontent;
		
	}
	
	public function processDefaultContent()
	{
		if(isset($_POST['default_content']))
		{
			$post_default_content = $_POST['default_content'];
		}
		else
		{
			$post_default_content = '';
		}
		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		$defaultContent = $xml->addChild('defaultcontent');
		$defaultContent->addCData($post_default_content); 
		if(XMLsave($xml, DCFile))
		{
			echo '<div class="updated">File Succesfully Written</div>';
		}
	}
	
	public function showAdminForm()
	{
		global $EDLANG, $EDOPTIONS, $toolbar, $EDTOOL,$SITEURL;
		?>
		<h3>Set Default Content</h3>
		<form action="" method="POST">
			<p>
				<label>Default Content:</label>
				<textarea id="post-content" name="default_content"><?php echo $this->getDefaultContent(); ?></textarea>
			</p>
			<p>
				<input type="submit" value="submit" class="submit">
		</form>
		<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
		  // missing border around text area, too much padding on left side, ...
		  $(function() {
		    CKEDITOR.replace( 'post-content', {
			        skin : 'getsimple',
			        forcePasteAsPlainText : false,
			        language : '<?php echo $EDLANG; ?>',
			        defaultLanguage : '<?php echo $EDLANG; ?>',
			        entities : true,
			        uiColor : '#FFFFFF',
					height: '500px',
					baseHref : '<?php echo $SITEURL; ?>',
			        toolbar : [ <?php echo $toolbar; ?> ]
					    <?php echo $EDOPTIONS; ?>
		    })
		  });
		</script>
		<?php
	}
}

function default_content()
{
	$DefaultContent = new DefaultContent;
	if(isset($_POST['default_content']))
	{
		$DefaultContent->processDefaultContent();
	}
	$DefaultContent->showAdminForm();
}

function show_default_content()
{
	$DefaultContent = new DefaultContent;
	if(!isset($_GET['id']))
	{
		global $content;
		$content = $DefaultContent->getDefaultContent();
	}
}

?>