<H4>Oorzaken</H4>
<form id="list-form" name="list-form">
<button id="submit-list">Opslaan</button>
<div id="oorzaken-list">
	<ul class="sortable">
		<?php $list_nr = 0; ?>
		<?php foreach ($top as $key => $value) { ?>
			<?php $list_nr++; ?>
			<li class="move-handler-li" id="list_<?php echo $list_nr; ?>"><div style="margin-left:10px;"><input type="text" placeholder="" name="<?php echo $top[$key]['_id']; ?>" value="<?php echo $top[$key]['naam']; ?>" style="width: 465px;" /></div>
				<ul>
				<?php foreach ($middle as $m_key => $m_value){ ?>					
					<?php foreach ($m_value as $suboorzaak_key => $suboorzaak_value) {?>
					<?php if ($m_key == $top[$key]['naam']) {?>	
						<?php $list_nr++; ?>						
						<li class="move-handler-li" id="list_<?php echo $list_nr; ?>"><div style="margin-left:10px;"><input type="text" placeholder="" name="<?php print($m_value[$suboorzaak_key]['_id']); ?>" value="<?php print($m_value[$suboorzaak_key]['naam']); ?>" style="width: 465px;" /></div>
							<ul>
							<?php foreach ($bottom as $b_key => $b_value){ ?>
								<?php foreach ($b_value as $suboorzaak2_key => $suboorzaak2_value) {?>
									<?php if ($b_key == $m_value[$suboorzaak_key]['naam']) {?>
										<?php $list_nr++; ?>
										<li class="move-handler-li" id="list_<?php echo $list_nr; ?>"><div style="margin-left:10px;"><input type="text" placeholder="" name="<?php print($b_value[$suboorzaak2_key]['_id']); ?>" value="<?php print($b_value[$suboorzaak2_key]['naam']); ?>" style="width: 465px;" /></div></li>
										<?php } ?>	
									<?php } ?>
								<?php } ?>
							</ul>
						</li>	
					<?php }} ?>	
				<?php } ?>
				</ul>
			</li>			
		<?php } ?>
		<li class="move-handler-li" id="list_8"><div class="editabel"style="margin-left:10px;"><input type="text"  name="8" placeholder="Nieuwe oorzaak of suboorzaken toevoegen" style="width: 465px;" value=""></div></li>
	</ul>
</div>

