<?php
$show = $this->message('spoiler') . ' (' . $this->message('show') . ')';
$hide = $this->message('spoiler') . ' (' . $this->message('hide') . ')';

$counter = rand();
$click  = "document.getElementById('spoilerContent-". $counter ."').style.display = (document.getElementById('spoilerContent-". $counter ."').style.display == 'block' ? 'none' : 'block');";
$click .= "this.innerHTML = (document.getElementById('spoilerContent-". $counter ."').style.display == 'block' ? '". $hide ."' : '". $show ."');"; ?>		
			
<div class="decoda-spoiler">
	<button class="decoda-spoilerButton" type="button" onclick="<?php echo $click; ?>"><?php echo $show; ?></button>
	
	<div class="decoda-spoilerContent" id="spoilerContent-<?php echo $counter; ?>" style="display: none">
		<?php echo $content; ?>
	</div>
</div>