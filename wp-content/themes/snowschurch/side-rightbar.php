<div class="rightbar">
    <div class="recentbar">
    	<div class="rightbar-titleplate">
			<div class="rightbar-title">RELATED ARTICLES</div>
		</div>
        <div class="rightbar-content">
			<div class="rightbar-content-container">
				<?php my_custom_submenu(); ?>
			</div>
		</div>
    </div>
	
    <div class="recentbar">
    	<div class="rightbar-titleplate">
			<div class="rightbar-title">FOLLOW US</div>
		</div>
        <div class="rightbar-content">
			<div class="rightbar-content-container">
				<span class="likescount"><?php getFBCountLikes(); ?></span><span class="devotees">&nbsp;DEVOTEES</span>
				<br>are following us in facebook. Join us by clicking the follow button.
			</div>
        </div>
    </div>	
	
    <div class="recentbar">
    	<div class="rightbar-titleplate">
			<div class="rightbar-title">JOIN MAILING LIST</div>
		</div>
        <div class="rightbar-content">
			<div class="rightbar-content-container">
				<?php mailchimpSF_signup_form(); ?>
			</div>
        </div>
    </div>
    <div class="rightbar-endscroll"></div>
</div>