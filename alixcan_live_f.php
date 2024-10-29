<?php
/*
Plugin Name: AlixcaN LiveFeed
Plugin URI: http://www.alixcan.net/wordpress/eklentiler/wordpress-canli-yayin-eklentisi-v1-0
Description: Alixcan.Net Wordpress sitenizden facebook, twitter tarzı feedler atmanızı sağlayan sistem.
Version: 1.0
Author: AlixcaN | Alican Ertürk
Author URI: http://www.alixcan.net
*/

$pluginadi = $_GET['plugin'];
$parcala = explode('_',$pluginadi);
if($_GET['action'] == 'activate' && $parcala[0]=='alixcan' && $parcala[1]=='live'){
	mysql_query("
CREATE TABLE IF NOT EXISTS `wp_alixlivefeed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `baslik` varchar(225) COLLATE utf8_turkish_ci NOT NULL,
  `resim` text COLLATE utf8_turkish_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;
");
}

function alixcan_live_feed_ali() {
if(isset($_GET['feedlist']) == 'alix_feed_list'){
		$sayfa_basina = 10;
	$sayfa_sor = mysql_query("SELECT COUNT(`id`) FROM `wp_alixlivefeed`");
	$sayfalar = ceil(mysql_result($sayfa_sor,0) / $sayfa_basina);
	
	$sayfa = (isset($_GET['alix_sayfa'])) ? (int)abs($_GET['alix_sayfa']) : 1;
	$basla = ($sayfa - 1) * $sayfa_basina;
	
	$sql = mysql_query("SELECT * FROM wp_alixlivefeed LIMIT $basla,$sayfa_basina");
	if(mysql_num_rows($sql)>0){echo '<h3>Gönderdiğiniz Feedler</h3>
	<table cellpadding="5" style="border:1px solid #ddd; margin-bottom:5px;" cellspacing="5">
	<tr>
		<td style="width:5%; font-weight:bold">ID</td>
		<td style="width:70%; font-weight:bold">Mesaj</td>
		<td style="width:25%; font-weight:bold">Tarih</td>
	</tr>
	';
		while ($row = mysql_fetch_object($sql)){
			echo '<tr>';
					echo '<td>'. $row->id.'</td>';
					echo '<td>'.$row->baslik.'</td>';
					echo '<td>'.$row->date.'</td>';
			echo '</tr>';
		}
	}	
	echo '</table>';
if($sayfalar>=1 && $sayfa <= $sayfalar){
echo '<div class="sayfalar">Sayfalar: ';
	$link = 'index.php?feedlist=alix_feed_list&';
	for($x=1; $x<=$sayfalar; $x++){
	
		echo '<a href="'.$link.'alix_sayfa='.$x.'">';
		echo ($x == $sayfa) ? '<span>'.$x.'</span> ': '<em>'.$x.'</em> ';
		echo '</a>';
	}
echo '</div>';
}
	
	
	
	echo '<p><a id ="upload_image" href="index.php">Feed Gönder</a></p>';
}elseif(isset($_GET['edit']) == 'dashboard_alix_live#dashboard_alix_live'){
	echo '<p>
		Kullanımı Cok Basit Ve Bloğuna Bağlı Bir Yazar İçin Gayet Hoş Bir Eklenti.<br />
		Facebooktaki "Ne Düşünüyorsunuz?" Mantığı İle Benzer. Bir Yazı, Resim Veya Hem Yazı Hem Resim Paylaşma İmkanı Sağlamaktadır.<br />
		Bu Yazıları
		<p style="margin-left:15px;">
				  [alixcan_live_feed] - Tüm Yazıları Listeler
			<br />[alixcan_live_feed id=""] - Belirlediğiniz Yazıyı İstediğiniz Yerde Listeler
		</p>
		Yukarıdaki Shortcodeları Kullanarak İstediğiniz Şekilde Listeletebilirsiniz.
	</p>';
} else{ ?>


<?php if($_POST['submittwit']){

$baslik	= $_POST['baslik'];
$resim	= $_POST['upload_image'];
$date   = $_POST['date'];
global $wpdb;

$veri_dizisi = array(
		'baslik' => $baslik, 
		'resim'	 => $resim,
		'date'   => $date
		);
$wpdb->insert( 'wp_alixlivefeed', $veri_dizisi );
echo 'Yazı Eklendi';

} /*submittwit bitimi */?>
<script>
	jQuery(document).ready(function() {

	jQuery('#upload_image_button').click(function() {
	 formfield = jQuery('#upload_image').attr('name');
	 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	 return false;
	});

	window.send_to_editor = function(html) {
	 imgurl = jQuery('img',html).attr('src');
	 jQuery('#upload_image').val(imgurl);
	 tb_remove();
	}
	});
</script>


<form action="" enctype="multipart/form-data" method="POST">
	
	<p>
		<label for="baslik">Başlık:<span style="color:red;font-size:9px">En Fazla 255 Karakter</span></label><br />
		<input type="text" name="baslik" id="baslik" style="width:100%" />
	</p>
	<p>
		<label for="upload_image">Resim:</label><br />
		<input id="upload_image" type="text" size="36" name="upload_image" value="" />
		<input id="upload_image_button" type="button" value="Resim Yükle" /><br />
		Resim Dosyası Yükleyebilirsiniz Yada Direk Link Yazabilirsiniz.<span style="display:block;font-size:9px;color:red;">Dosya Yüklendikten Sonra Yazıya Dahil Et Butonuna Basınız Link Otomatik Eklenicektir</span>
	</p>
		<input type="hidden" id="date" name="date" value="<?php echo date("Y-m-d G:i:s");?>" />
	<p class="submit">
		<input type="submit" name="submittwit" id="submittwit" />
	</p>

</form>
<p><a id ="upload_image" href="index.php?feedlist=alix_feed_list">Feedleri Listele</a></p>
<?php
} // else
}  // function

function alixcan_live_feed_setup() {
	$yazi = (isset($_GET['edit']) == 'dashboard_alix_live#dashboard_alix_live') ? '<a href="index.php">Kapat</a>' : '<a href="index.php?edit=dashboard_alix_live#dashboard_alix_live" class="edit-box open-box">Hakkında</a>';
	wp_add_dashboard_widget( 'alixcan_live_feed_ali', __( 'Canlı Yayın & Live Feed<span class="postbox-title-action">'.$yazi.'</span>' ), 'alixcan_live_feed_ali' );
}
add_action('wp_dashboard_setup', 'alixcan_live_feed_setup');

function head_ekle(){
	echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/alixcan_live_f/style.css" type="text/css" />';
}
add_action('wp_head', 'head_ekle');


add_shortcode('alixcan_live_feed', 'alixcan_live_feed_shortcode');
function alixcan_live_feed_shortcode( $atts, $content = null){
	global $post;
	extract( shortcode_atts( array( 'id' => '' ) , $atts ) );

	if(empty($id)){
			
 		
	$sayfa_basina = 10;
	$sayfa_sor = mysql_query("SELECT COUNT(`id`) FROM `wp_alixlivefeed`");
	$sayfalar = ceil(mysql_result($sayfa_sor,0) / $sayfa_basina);
	
	$sayfa = (isset($_GET['alix_sayfa'])) ? (int)abs($_GET['alix_sayfa']) : 1;
	$basla = ($sayfa - 1) * $sayfa_basina;
	
	$sql = mysql_query("SELECT * FROM wp_alixlivefeed LIMIT $basla,$sayfa_basina");
	if(mysql_num_rows($sql)>0){echo '<div id="alixcan">
			<ul id="list">';
	while ($row = mysql_fetch_object($sql)){
		echo '<li>';
					echo (!empty($row->resim)) ? '<a href="'.$row->resim.'" target="_blank" title="'.$row->baslik.'"><img src="'.$row->resim.'" /></a>' : '';
					echo $row->baslik.'<br /><em>'.$row->date.'</em>
					<div style="clear:both;"></div>
					</li>
					';
	}echo '</ul>';
	}else{
		echo '<div style="display:block;float:none;">Henüz İçerik Girilmemiş</div>';
	}
	
	
if($sayfalar>=1 && $sayfa <= $sayfalar){
echo '<div class="sayfalar">Sayfalar: ';
	$link = get_option('home'). '?p='. get_the_ID();
	for($x=1; $x<=$sayfalar; $x++){
	
		echo '<a href="'.$link.'&alix_sayfa='.$x.'">';
		echo ($x == $sayfa) ? '<span>'.$x.'</span> ': '<em>'.$x.'</em> ';
		echo '</a>';
	}
echo '</div>';
}
	echo '</div>';
 
	}else{
		
		 $sqlsor = mysql_query("SELECT * FROM wp_alixlivefeed WHERE id='$id'");
			$row = mysql_fetch_object($sqlsor);
			echo '<div id="alixcan">
			<ul id="list">';
			echo '<li>';
			echo (!empty($row->resim)) ? '<a href="'.$row->resim.'" target="_blank" title="'.$row->baslik.'"><img src="'.$row->resim.'" /></a>' : '';
			echo $row->baslik.'<br /><em>'.$row->date.'</em>
			<div style="clear:both;"></div>
			</li>
			</ul>
			</div>';
		
	}//else
}// func biter