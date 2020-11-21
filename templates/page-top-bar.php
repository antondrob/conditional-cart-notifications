<div class="ccn-page-header">
	<div class="ccn-page-header__left">
		<div class="ccn-page-tabs">
			<a href="<?= admin_url( 'edit.php' ) . '?post_type=' . $this->post_type_name; ?>" class="<?php if( $_GET['post_type'] == $this->post_type_name ) { echo 'active'; } ?>">Messages</a>
			<a href="<?= admin_url( 'admin.php' ) . '?page=ccn_layouts'; ?>" class="<?php if( $_GET['page'] == 'ccn_layouts' ) { echo 'active'; } ?>">Layouts</a>
			<a href="<?= admin_url( 'admin.php' ) . '?page=ccn_upgrade'; ?>" class="<?php if( $_GET['page'] == 'ccn_upgrade' ) { echo 'active'; } ?>">Upgrade</a>
        </div>
        
		<div class="ccn-notification ccn-border">
			<a href="#" class="ccn-notification__close">✖</a>
            <span class="ccn-notification__label ccn-border__label">Welcome</span>
            <h4 class="ccn-notification__title">Welcome to Woo Shopping Messages!</h4>
            <div class="ccn-notification__text">
                Launch our installation wizard to quickly and easily configure the shopping messages for your 
                site. Browse our video guides to go further. Can't find the answers to your questions? Open a 
                ticket from your customer area. A happiness engineer will be happy to help you.
            </div>
		</div>			
    </div>
    
	<div class="ccn-page-header__right">
		<div class="ccn-version">
			<img src="<?= $this->plugin_dir() . 'img/smile.png'; ?>">
			<p class="ccn-version__title">Free Version</p>
		</div>
	</div>		
</div>

<?php if($current_page->post_type == $this->post_type_name): ?>
	<div class="ccn-page-buttons">


	    <?php if ($current_page->post_type == $this->post_type_name && $pagenow == 'edit.php' ): ?> 
	    	<a href="#" class="active">All messages</a>
		<?php else: ?>
			<a href="<?php echo get_home_url() . '/wp-admin/edit.php?post_type=' . $this->post_type_name; ?>">All messages</a>
		<?php endif; ?>

		<?php if ( ($current_page->post_type == $this->post_type_name && $pagenow == 'post.php') || ($current_page->post_type == $this->post_type_name && $pagenow == 'post-new.php')  ): ?> 
	    	<a href="#" class="active">Edit message</a>
		<?php endif; ?>	

		<a href="#" id="add-new-ccn-message">Add new message</a>

	</div>
<?php endif; ?>

<?php if( $_GET['page'] == 'ccn_layouts'): ?>
	<div class="ccn-page-buttons">
		<a href="<?= admin_url( 'admin.php' ) . '?page=ccn_layouts'; ?>" class="active">ALL LAYOUTS</a>
		<a href="#" id="add-new-ccn-message">ADD NEW LAYOUT (PRO)</a>
	</div>
<?php endif; ?>