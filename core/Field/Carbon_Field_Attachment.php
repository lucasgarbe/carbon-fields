<?php 

class Carbon_Field_Attachment extends Carbon_Field_File {
	public $value_type = 'id';

	function template_description() {
		?>
		<div class="carbon-description {{{ value ? '' : 'hidden' }}}">
			<div class="carbon-attachment-preview {{{ thumb_url ? '' : 'hidden' }}}">
				<div class="carbon-preview">
					<div class="thumbnail">
						<div class="centered">
							<img src="{{ thumb_url }}" class="thumbnail-image" />
						</div>
					</div>
					<div class="carbon-file-remove"></div>
				</div>
			</div>

			<# if (value_type === 'id') { #>
				<div class="attachment-url">{{ url }}</div>
			<# } #>
		</div>
		<?php
	}
}