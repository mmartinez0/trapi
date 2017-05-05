<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<div id="video-top" class="wrapper video-wrapper">
    <div class="container-fluid">

        <div id="video-grid" class="row video-grid animate-show" ng-hide="viewModel.video">
            <div class="col-lg-12">
            	<h1>Videos</h1>
            	<p><a id="add-video" class="btn btn-primary" ng-click="videoModel.addVideo()">Add Video</a></p>
            	<br/>
				<table videos ng-model="viewModel.videos" class="table table-striped table-bordered"></table>
			</div>
		</div>

        <div id="video-form" class="row video-form animate-show" ng-show="viewModel.video">
            <div class="col-lg-12">
            	<h1>Video {{ viewModel.video.id }}  <button type="submit" class="btn btn-link btn-xs" ng-click="viewModel.cancel()">Cancel</button></h1>

				<form>
					<div class="form-group">
						<label for="post_title">Title</label>
			    		<input type="text" class="form-control" id="post_title" placeholder="Title" ng-model="viewModel.video.post_title"/>			
					</div>
					<div class="form-group">
						<label for="post_content">Description</label>
						<textarea id="post_content" class="form-control" rows="3" ng-model="viewModel.video.post_content" placeholder="Description"></textarea>
					</div>
					<div class="form-group">
						<label for="media_type_id">Media Type</label>
			    		<select class="form-control" id="media_type_id" ng-model="viewModel.video.media_type_id">
			    			<option value=""></option>
			    			<option value="3">Flash Video File</option>
			    			<option value="4">MP3 Music File</option>
			    			<option value="6">PDF Document</option>
			    			<option value="10">HTML Content</option>
			    		</select>
					</div>
					<div class="form-group">
						<label for="video_file_url">Video/Audio File URL {{ viewModel.video.video_file_url_type }}</label>

						<span ng-show="viewModel.video.video_file_url_type">
							<a class="btn btn-link btn-xs" ng-click="viewModel.playVideo('video_file_url')">play</a>
						</span>
			    		<input type="text" class="form-control" id="video_file_url" placeholder="Video/Audio File URL" ng-model="viewModel.video.video_file_url"/>			
					</div>

					<div class="form-group">
						<label for="mobile_video_file_url">Mobile Video File URL {{ viewModel.video.mobile_video_file_url_type }}</label>
						
						<span ng-show="viewModel.video.mobile_video_file_url_type">
							<a class="btn btn-link btn-xs btn-play-video" ng-click="viewModel.playVideo('mobile_video_file_url')">play</a>
						</span>
			    		<input type="text" class="form-control" id="mobile_video_file_url" placeholder="Mobile Video File URL" ng-model="viewModel.video.mobile_video_file_url"/>
					</div>
					<div class="form-group" ng-class="{ 'hidden': false }">
						<label for="thumbnail_id">thumbnail_id</label>
			    		<input type="text" class="form-control" id="thumbnail_id" placeholder="thumbnail_id" ng-model="viewModel.video.thumbnail_id"/>			
					</div>
					<div class="form-group">
						<label for="media_status">Video Poster </label> 
						<a class="btn btn-link btn-xs" ng-click="viewModel.selectFile($event)">Upload</a>
						<a class="btn btn-link btn-xs" ng-click="viewModel.showVideoLibrary()">Library</a>
						<input type="file" file-select="onFileSelect($files)" id="fileSelector" ng-class="{ 'hidden': true }" />
						<div id="video-poster"></div>
						<em>thumbnail id: {{ viewModel.video.thumbnail_id }}</em>
					</div>
					<div class="form-group" ng-class="{ 'hidden': true }">
						<label for="previewfile">Preview File</label>
			    		<input type="text" class="form-control" id="previewfile" placeholder="Preview File" ng-model="viewModel.video.previewfile"/>			
					</div>
					<div class="form-group">
						<label for="html_content_url">HTML Content URL</label>
			    		<input type="text" class="form-control" id="html_content_url" placeholder="HTML Content URL" ng-model="viewModel.video.html_content_url"/>			
					</div>
					<div class="form-group">
						<label for="file_url">File URL</label>
			    		<input type="text" class="form-control" id="file_url" placeholder="File URL" ng-model="viewModel.video.file_url"/>			
					</div>
					<div class="form-group">
						<label for="buy_now_url">Buy Now URL</label>
			    		<input type="text" class="form-control" id="buy_now_url" placeholder="Buy Now URL" ng-model="viewModel.video.buy_now_url"/>			
					</div>
					<div class="form-group">
						<label for="player_presenter">Player/Presenter</label>
			    		<input type="text" class="form-control" id="player_presenter" placeholder="Player/Presenter" ng-model="viewModel.video.player_presenter"/>			
					</div>
					<div class="form-group">
						<label for="video_length">Video Length (min:sec)</label>
			    		<input type="text" class="form-control" id="video_length" placeholder="Video Length (min:sec)" ng-model="viewModel.video.video_length"/>			
					</div>
					<div class="form-group">
						<label for="credit_video_id">Credit Video ID</label>
			    		<input type="text" class="form-control" id="credit_video_id" placeholder="Credit Video ID" ng-model="viewModel.video.credit_video_id"/>			
					</div>
					<div class="form-group">
						<label for="credit_video_price">Credit Video Price (Non-Member)</label>
			    		<input type="text" class="form-control" id="credit_video_price" placeholder="Credit Video Price (Non-Member)" ng-model="viewModel.video.credit_video_price"/>			
					</div>
					<div class="form-group">
						<label for="member_price">Credit Video Price (Member)</label>
			    		<input type="text" class="form-control" id="member_price" placeholder="Credit Video Price (Member)" ng-model="viewModel.video.member_price"/>			
					</div>
					<div class="checkbox">
						<label for="featured">
							<input id="featured" type="checkbox" ng-model="viewModel.video.featured" true-value="'Yes'" false-value="'No'"> Featured
						</label>
					</div>
					<div class="form-group">
						<label for="media_status">Media Status</label>
			    		<select class="form-control" id="media_status" ng-model="viewModel.video.media_status">
			    			<option value=""></option>
			    			<option value="0">Disabled</option>
			    			<option value="1">Active</option>
			    		</select>
					</div>

					<div class="form-group">
						<label for="post_date">Create Date</label><br/>
						{{ viewModel.video.post_date }}
			    		
					</div>
					<div class="form-group">
						<label for="post_date">Modified Date</label><br/>
						{{ viewModel.video.post_modified }}
					</div>
					<button type="submit" class="btn btn-primary" ng-click="viewModel.save()">Save</button>
					<button type="submit" class="btn btn-link" ng-click="viewModel.cancel()">Cancel</button>
				</form>
            </div>
        </div>

	</div>
</div>

<div bn-modals ng-show="subview" class="m-modals" ng-switch="subview">
	<div ng-switch-when="player" ng-controller="PlayerController" class="modal player-dialog">
		<div class="panel panel-default">
			<div class="panel-heading">Capture Poster</div>
			<div class="panel-body">
				<form>
					<div id="video-here" class="form-group">
						<div id="trapi-video"></div>
						<button class="btn btn-primary btn-xs" ng-click="capture()">Capture</button> 
					</div>
					<div class="form-group" ng-class="{ 'hidden': true }">
						<div id="canvas-wrapper">
							<canvas id="canvas"></canvas> 
						</div>
					</div>
				</form>
		  	</div>
		</div>		
	</div>

	<div ng-switch-when="library" ng-controller="LibraryController" class="modal library-dialog">
		<div class="panel panel-default">
			<div class="panel-heading">Library</div>
			<div class="panel-body">
				<div image-library ng-model="library"></div>
		  	</div>
		</div>		
	</div>
</div>
