<div class="wrap">
<form id="posts-filter" method="get">

<table class="wp-list-table widefat fixed striped table-view-list posts">
	<thead>
	<tr>
		<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td><th scope="col" id="title" class="manage-column column-title column-primary sortable desc"><a href="#"><span>Name</span><span class="sorting-indicator"></span></a></th><th scope="col" id="date" class="manage-column column-date sortable asc"><a href="#"><span>Date Modified</span><span class="sorting-indicator"></span></a></th><th scope="col" id="type" class="manage-column column-type">Messages</th>
	</tr>
	</thead>

	<tbody id="the-list">
		<?php 
			$layout_colors = $this->layout_colors_data();
		?>
		<?php foreach ($layout_colors as $layout_slug => $layout): ?>
		
			<tr id="layout-<?= $layout['title']; ?>" class="iedit author-self level-0 type-ccn_message status-publish hentry">
				<th scope="row" class="check-column">			
					<label class="screen-reader-text" for="cb-select-153">Select <?= $layout['title']; ?></label>
				<input id="cb-select-153" type="checkbox" name="post[]" value="153">
				<div class="locked-indicator">
					<span class="locked-indicator-icon" aria-hidden="true"></span>
					<span class="screen-reader-text">“<?= $layout['title']; ?>” is locked</span>
				</div>
				</th>
				<td class="title column-title has-row-actions column-primary page-title" data-colname="Name">
					<div class="locked-info">
						<span class="locked-avatar"></span>
						<span class="locked-text"></span>
					</div>
					<strong><a class="row-title" href="#" aria-label="“Message 2” (Edit)"><?= $layout['title']; ?></a></strong>

					<div class="row-actions">
						<span class="edit">
							<a href="#" aria-label="Edit “Message 2”">Edit</a> | 
						</span>
						<span class="inline hide-if-no-js">
							<button type="button" class="button-link editinline" aria-label="Quick edit “Message 2” inline" aria-expanded="false">Quick&nbsp;Edit</button> | 
						</span>
						<span class="trash">
							<a href="#" class="submitdelete" aria-label="Move “Message 2” to the Trash">Trash</a> | 
						</span>
						<span class="duplicate">
							<a href="#" title="Duplicate this item" rel="permalink">Duplicate</a>
						</span>
					</div>
					<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
				</td>
				<td class="date column-date" data-colname="Date Modified">Published<br>2020/09/28 at 11:48 pm</td>
				<td class="type column-type" data-colname="Message Type">
				<?php 
					$args = array(
						'post_type'     => $this->post_type_name,
						'posts_per_page'=> -1,
						'orderby'       => 'id',
						'order'         => 'DESC',
						'meta_query'	=> array(
							'relationship'	=> 'AND',
							array(
								'key'     => 'ccn_message_layout',
								'value'   => $layout_slug,
								'compare' => '=',
							),
						),
					);
					$messages = new WP_Query($args);
					echo $messages->found_posts;
				?>
				</td>		
			</tr>

		<?php endforeach; ?>
	</tbody>

	<tfoot>
	<tr>
		<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></td><th scope="col" class="manage-column column-title column-primary sortable desc"><a href="#"><span>Name</span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-date sortable asc"><a href="manage-column column-title column-primary sortable desc"><span>Date Modified</span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-type">Messages</th>
	</tr>
	</tfoot>

</table>
		
</form>
</div>