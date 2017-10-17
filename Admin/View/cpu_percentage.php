<?php $i = 0; ?>
<?php foreach ($data as $index => $cpu): ?>
	<?php if ($i % 2 == 0) : ?>
		<div style="width: 100%">
	<?php endif; ?>
	<div id="<?php echo $index; ?>" style="display: inline-block; width: 50%;"></div>
	<?php $i += 1; ?>
	<?php if ($i % 2 == 0) : ?>
		</div>
	<?php endif; ?>
<?php endforeach; ?>
<script>
	$(document).ready(function () {
		<?php foreach ($data as $index => $cpu): ?>
		$('#<?php echo $index; ?>').height($('#<?php echo $index; ?>').width());
		<?php endforeach; ?>
		<?php foreach ($data as $index => $cpu): ?>
		var <?php echo $index; ?> =
		echarts.init(document.getElementById('<?php echo $index; ?>'));
		<?php echo $index; ?>.
		setOption({
			title: {
				text: '<?php echo $index; ?>',
				x: 'center',
				y: 'center',
				textStyle: {
					fontSize: '100%',
					fontWeight: 'bold'
				}
			},
			tooltip: {
				trigger: 'item',
				formatter: "{a}<br/>{b}: {d}%"
			},
			legend: {
				show: false,
				x: 'center',
				y: 'bottom',
				data: ['User', 'Nice', 'Sys', 'Idle', 'Iowait']
			},
			series: [
				{
					name: '<?php echo $index; ?>',
					type: 'pie',
					radius: ['50%', '90%'],
					avoidLabelOverlap: false,
					label: {
						normal: {
							show: false,
							position: 'center'
						},
						emphasis: {
							show: false,
							textStyle: {
								fontSize: '30',
								fontWeight: 'bold'
							}
						}
					},
					data: [
						<?php foreach ($cpu as $name => $key):  ?>
						{value: <?php echo $key; ?>, name: '<?php echo ucfirst($name); ?>'},
						<?php endforeach; ?>
					]
				}
			]
		});
		<?php endforeach; ?>

		getJSONData();

		function getJSONData() {
			setTimeout(function () {
				getJSONData()
			}, 1000);
			Ajax({service: 'Tz.GetCpuPercentage'}, function (d) {
				if (d.ret == 200) {
					result = d.data;
					console.log(result);
					<?php foreach ($data as $index => $cpu): ?>
					var datas = [];
					console.log(result['<?php echo $index; ?>']);
					for (var key in result['<?php echo $index; ?>']) {
						var val = result['<?php echo $index; ?>'][key];
						var da = {};
						da.value = val;
						da.name = key;
						datas.push(da);
					}
					console.log(datas);
					<?php echo $index; ?>.
					setOption({
						series: [{
							name: '<?php echo $index; ?>',
							data: datas
						}]
					});
					<?php endforeach; ?>
				}
			})
		}

	});


</script>
