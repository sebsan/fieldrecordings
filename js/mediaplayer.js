/* Media Player */




function initMediaPlayer()
{
	jQuery('.audio_block').each(function(index)
	{
		var wp_id =  jQuery(this).attr('id').substring(6);
		var audioFile = jQuery(this).attr('title');
		var playerDiv = jQuery(this).children('.media_player').first();
		var audioFileExt = audioFile.substring(audioFile.length - 3 , audioFile.length);
// 		alert('ext: ' + audioFileExt);
		if(audioFileExt == 'mp3')
		{
			playerDiv.jPlayer({
				ready: function () 
				{
					jQuery(this).jPlayer("setMedia", { mp3: audioFile});
				},
				supplied: "mp3",
				swfPath: jplayerswf,
				cssSelectorAncestor: '#jp_interface_' + wp_id,
				cssSelector: {
				play: '.jp-play',
				pause: '.jp-pause'
				}
				});
		}
		else
		{
			playerDiv.jPlayer({
				ready: function () 
				{
// 					alert('oops: UN ogg!');
					jQuery(this).jPlayer("setMedia", { oga: audioFile});
				},
				supplied: "oga",
				swfPath: jplayerswf,
				cssSelectorAncestor: '#jp_interface_' + wp_id,
				cssSelector: {
				play: '.jp-play',
				pause: '.jp-pause'
				}
			});
		}
	});
}


jQuery(document).ready(initMediaPlayer);