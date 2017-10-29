<div class="modal-content">
	<h4 class="center"><?php echo $delivery['log_name']; ?></h4>
	<h5 class="center"><?php echo $logistics['nu']; ?></h5>
	<ul class="collapsible" data-collapsible="accordion">
		<?php
		//$data = array_reverse($logistics['data']);
		$data = $logistics['data'];
		foreach ($data as $key => $item) : ?>
			<li>
				<div class="collapsible-header">
					<?php switch ($key):
						case (count($data) - 1):
							?>
							<i class="material-icons">adjust</i>
							<?php break;
						case 0:
							if ($logistics['state'] == 3) :?>
								<i class="material-icons">done</i>
							<?php else: ?>
								<i class="material-icons">label_outline</i>
							<?php endif; ?>
							<?php break;
						default: ?>
							<i class="material-icons">label_outline</i>
							<?php break; endswitch; ?>
					<?php echo $item['context']; ?>
				</div>
				<div class="collapsible-body">
					<span><?php echo $item['context']; ?></span>
					<br/>记录时间：
					<span><?php echo $item['time']; ?></span>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</div>