/**
 * JavaScript for Bulk move Plugin
 *
 *
 *
 *
 *
 */

/*jslint browser: true, devel: true*/
/*global BULK_MOVE, jQuery, document, postboxes, pagenow*/
(function($) {

	//$(function() {

	setMessage = function (msg) {
		$("#message").html(msg);
		$("#message").show();
	}

	setUpAjaxButtons = function() {
		console.log('setUpAjaxButtons');
		$(".bm_ajax_action").each(function() {
			// console.log("bm_ajax_action found...");

			$(this).on('click', function (e) {
				e.preventDefault();
				// console.log('clicky click');
				var moveType = $(this).data("movetype");
        // alert("moveType=="+moveType);
        //processBatch(moveType);
        //we have to know a bit more than jsut what type of move it is ... we have to know specifically what to what it is
        //select names (cats) smbm_mc_selected_cat, smbm_mc_mapped_cat
        //select names: smbm_mt_old_tag, smbm_mt_new_tag
        //and we can mix and match from there
        //so ... based on the type, we know which fieldgroup? to aquire values from, and what values to acquire
        //#bm_move_category, #bm_move_tag, #bm_move_category_by_tag, #bm_move_tag_by_category
        //and then we know what to look for in there.
        //cats-cats, tags-tags, tags-cats, cats-tags
        var fromWhat = '';
        var toWhat = '';
        var whetherOrNotToRemoveFromCatOrTag = '';//default
        switch(moveType) {
        	case 'cats-cats':
        		fromWhat = $("#bm_move_category select[name='smbm_mc_selected_cat']").val();
        		toWhat = $("#bm_move_category select[name='smbm_mc_mapped_cat']").val();
        		whetherOrNotToRemoveFromCatOrTag = $("#bm_move_category input[name='smbm_mc_overwrite']:checked").val();//overwrite or no-overwrite
        		statusMsgContainer = $("#bm_move_category .bm_ajax_status");
        		break;
        	case 'tags-tags':
        		fromWhat = $("#bm_move_tag select[name='smbm_mt_old_tag']").val();
        		toWhat = $("#bm_move_tag select[name='smbm_mt_new_tag']").val();
        		whetherOrNotToRemoveFromCatOrTag = $("#bm_move_tag input[name='smbm_mct_overwrite']:checked").val();//overwrite or no-overwrite
        		statusMsgContainer = $("#bm_move_tag .bm_ajax_status");
        		break;
        	case 'tags-cats':
        		fromWhat = $("#bm_move_category_by_tag select[name='smbm_mtc_old_tag']").val();
        		toWhat = $("#bm_move_category_by_tag select[name='smbm_mtc_mapped_cat']").val();
        		whetherOrNotToRemoveFromCatOrTag = $("#bm_move_category_by_tag input[name='smbm_mtc_overwrite']:checked").val();//overwrite or no-overwrite
						// console.log('here we are in tags-cats ... trying to make a statusMsgContainer');
        		statusMsgContainer = $("#bm_move_category_by_tag .bm_ajax_status");
						// console.log('statusMsgContainer==');
						// console.log(statusMsgContainer);
        		break;
        	case 'cats-tags':
        		fromWhat = $("#bm_move_tag_by_category select[name='smbm_mc_selected_cat']").val();
        		toWhat = $("#bm_move_tag_by_category select[name='smbm_mt_new_tag']").val();
        		whetherOrNotToRemoveFromCatOrTag = $("#bm_move_tag_by_category input[name='smbm_mct_overwrite']:checked").val();//overwrite or no-overwrite ... returning undefined?wtf?!
        		statusMsgContainer = $("#bm_move_tag_by_category .bm_ajax_status");
        		break;
        }

        processBatch(moveType, fromWhat, toWhat, whetherOrNotToRemoveFromCatOrTag, statusMsgContainer);



      });
		});
	}

	processBatch = function (whichBatchType, fromWhat, toWhat, whetherOrNotToRemoveFromCatOrTag, statusMsgContainer) {
		console.log('processBatch(' + whichBatchType + ', ' + fromWhat + ',' + toWhat + ', ' + whetherOrNotToRemoveFromCatOrTag + ')');
		if (Number(fromWhat) <= 0 || Number(toWhat) <= 0) {
			return false;
		}
		var ajaxEndPoint = "/wp-admin/admin-ajax.php";
		var maxChunkSize = 10;//the max number of items to send in a single moveItem request
		jQuery.ajax({
			url: ajaxEndPoint,
			type: "POST",
			data: "action=ajax_bulk_move&do=getlist&batchType=" + whichBatchType + "&from="+fromWhat+"&to="+toWhat,
			success: function(result) {
				// console.log(result);
				var list = eval(result);
				var curr = 0;

				if (!list) {
					//setMessage("<?php _e('No attachments found.', 'ajax-bulk-move')?>");
					//jQuery("#ajax_bulk_move").prop("disabled", false);//we need to make all of them disabled
					//disable all move buttons
					return;
				}

				function moveItems() {
					//console.log('curr=='+curr);
					if (curr >= list.length) {
						//jQuery("#ajax_bulk_move").prop("disabled", false);//re-enable all of them again
						//re-enable all move buttons
						//setMessage("<?php _e('Done.', 'ajax-bulk-move') ?>");
						return;
					}
					//setMessage(<?php printf( __('"Moving " + %s + " of " + %s + " (" + %s + ")..."', 'ajax-bulk-move'), "(curr+1)", "list.length", "list[curr].title"); ?>);
					//one strategy, would be to give the decision making power to the client. probably, we would want to secure these ajax calls, w a nonce, so that wp know that this person in particular has privs to do this
					//also, we could let it send an array of post ids
					//let's do it one at a time for now, and see how that goes.

					//we need to send the items in a url string that we can explode when it arrives ... so it needs some sort of delimeter a , or a +
					//lets call them ids instead of id
					//construct the chunk
					var chunkString = '';
					for (var i=0; i<maxChunkSize; i++) {
						if (curr >= list.length) {
							break;
						}
						chunkString += list[curr];
						/*if (curr < list.lenth) {
							chunkString += ',';
						}*/
						if ( i+1 < maxChunkSize) {
							chunkString += ',';
						}

						curr++;
					}


					jQuery.ajax({
						url: ajaxEndPoint,
						type: "POST",
						data: "action=ajax_bulk_move&do=move_item&ids=" + chunkString + '&batchType=' + whichBatchType + '&from=' + fromWhat + '&to=' + toWhat + '&doWeOverwrite=' + whetherOrNotToRemoveFromCatOrTag,
						success: function(result) {
							//console.log("moveItem: success!");
							//curr = curr + 1;
							if (result != '-1') {
								//jQuery("#thumb").show();
								//jQuery("#thumb-img").attr("src",result);
								//bm_ajax_status

								//we need to set the dom item that is to receive the update messages, and use that
								if (curr >= list.length) {
									//$("#bm_move_category .bm_ajax_status").text( "Done.");
									statusMsgContainer.text( "Done.");
								} else {
									statusMsgContainer.text( curr + "/" + list.length);
								}

							}
							moveItems();
						},
						error: function (request, status, error) {
							setMessage("Error:" + request.status);
						}
					});
				}
				moveItems();//start the recursive moveItem chain going
			},
			error: function(request, status, error) {
				setMessage("Error:" + request.status);
			}
		});
	}

	$(function(){//equivalent to the on $(document).ready
		$('button[value="bulk-move-tags"], button[value="bulk-move-cats"], button[value="bulk-move-category-by-tag"], button[value="bulk-move-tag-by-category"]').click(function () {
				return confirm(BULK_MOVE.msg.move_warning);//confirm is a wp function?
		});

		// for post boxes
		postboxes.add_postbox_toggles(pagenow);

		setUpAjaxButtons();




	});



})(jQuery);
