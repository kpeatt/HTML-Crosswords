<h2>Create a news item</h2>

<?php echo validation_errors(); ?>

<?php echo form_open('news/create') ?>

	<div class="element">
		<label for="title">Title</label>
		<input type="input" name="title">
	</div>
	
	<div class="element">
		<label for="text">Text</label>
		<textarea name="text"></textarea>
	</div>
	
	<div class="element">
		<input type="submit" name="submit" value="Create news item">
	</div>
	
</form>
	
	