/**
 * Media Queries Monitor script for WordPress Admin Bar
 * Created by ilovecolors.com.ar
 * Follow me on Twitter twitter.com/eliorivero 
 */

jQuery(document).ready(function($) {
	
	$('#wp-admin-bar-root-default').prepend('<li><a class="mqmonitor" href="#">'+ilcmqm.lbl+'</a></li>');
	
	ilcmqs = ilcmqm.mq.split(',');
	
	if( $(window).width() > ilcmqm.fs )
		$('.mqmonitor').text( ilcmqm.fs );
	
	for( i = 0; i < ilcmqs.length; i++ )
		if ( $(window).width() < ilcmqs[i] )
			$('.mqmonitor').text( ilcmqs[i] );
	
	$(window).resize(function(){
		
		for( i = 0; i < ilcmqs.length; i++ )
			if ( $(window).width() < ilcmqs[i] )
				$('.mqmonitor').text( ilcmqs[i] );
	});
	
});