/**
*
* @package phpBB Extension - Smilies ALbum
* @copyright (c) 2016 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

function AJAXSAMVote(sam_id, points){
	$.ajax({
        url: sam_ajax_url + '&sam_id=' + sam_id + '&points=' + points,
		type: "GET",
        success: function(data) { AJAXSAMFinish(data); }
	});
}

function AJAXSAMFinish(data) {
	var obj = $.parseJSON( data );
	$( "#rating" ).html( obj.rating_img );
}
