<?php
class pant_login extends toba_ei_pantalla
{
	function generar_layout()
	{
		if ($this->existe_dependencia('seleccion_usuario')) {
			$this->dep('seleccion_usuario')->generar_html();
		}


		/*echo '
<table style="width: 100%;">
<tr>
  	<td style="width: 50%;">

		<div class="fb-page" data-href="https://www.facebook.com/pages/Centro-Inform%C3%A1tico-y-Tecnol%C3%B3gico-CIT/763426333784782" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/pages/Centro-Inform%C3%A1tico-y-Tecnol%C3%B3gico-CIT/763426333784782"><a href="https://www.facebook.com/pages/Centro-Inform%C3%A1tico-y-Tecnol%C3%B3gico-CIT/763426333784782">Centro Informático y Tecnológico CIT</a></blockquote></div></div>

		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.4";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, \'script\', \'facebook-jssdk\'));</script>

	</td>
	<td style="width: 50%; vertical-align: top; padding-left: 20px;">
		';*/




/*.ei-form-base {
	background: rgb(255, 255, 255); 
    background: rgba(255, 255, 255, .5);
}*/

echo '
<style TYPE="text/css">
<!--
.ci-simple-cont {
    padding: 0px;
}

.ei-form-base {
	background: #176565; 
}

 -->
</style>
';


echo "<div class='login-titulo' style='width:100%; text-align: center;'>";

echo '<table style="width: 600px; margin: 0 auto; text-align: left;">
<tr>
  	<td style="width: 50%;">';

echo toba_recurso::imagen_proyecto("logo_login.png",true); 

echo '</td><td>';

		echo '<div>
		';		
		if ($this->existe_dependencia('datos')) {
			echo "<div style='float:left;'>";
			$this->dep('datos')->generar_html();
			echo '</div>';
		} 
		if ($this->existe_dependencia('openid')) {
			echo "<div style='margin-left: 30px; float:right;'>";			
			$this->dep('openid')->generar_html();
			echo '</div>';
		}
		if ($this->existe_dependencia('cas')) {
			echo "<div style='margin-left: 30px; float:right;'>";			
			$this->dep('cas')->generar_html();
			echo '</div>';
		}		
		echo '</div>';
		//-----------------------------------------------------

echo '</td>
</tr>
</table>';

echo "</div>";


echo "<div align='center' class='cuerpo' style='
	padding-top: 30px;
    background-color: #F2F2F2;
    margin: 0px;
    padding-bottom: 30px;'>\n";

echo '<div class="fb-page" data-href="https://www.facebook.com/pages/Centro-Inform%C3%A1tico-y-Tecnol%C3%B3gico-CIT/763426333784782" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/pages/Centro-Inform%C3%A1tico-y-Tecnol%C3%B3gico-CIT/763426333784782"><a href="https://www.facebook.com/pages/Centro-Inform%C3%A1tico-y-Tecnol%C3%B3gico-CIT/763426333784782">Centro Informático y Tecnológico CIT</a></blockquote></div></div>


<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.4";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';

echo '</div>';

	}

}

?>