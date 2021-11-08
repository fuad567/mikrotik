<div class="area_program">
<h4>Generator Simple Queue Metode PCQ</h4>
<form method="POST">
	<table>
		<tr>
			<td style="width:180px">Custom Nama Grup</td>
			<td><input type="text" name="grup_name" value="5 Mbps"></td>
		</tr>
		<tr>
			<td>Custom Nama Client</td>
			<td><input type="text" name="custom_name" value="CLIENT"></td>
		</tr>
		<tr>
			<td>Network IP</td>
			<td><input type="text" name="network" value="10.11.11"></td>
		</tr>
		<tr>
			<td>IP Mulai - Akhir</td>
			<td><input type="text" name="start" value="2" style="width:71px"> - <input type="text" name="end" value="254" style="width:71px"></td>
		</tr>
		<tr>
			<td>Jumlah Client per Grup</td>
			<td><input type="text" name="client_per_grup" value="5"></td>
		</tr>
		<tr>
			<td>Max Limit Grup</td>
			<td><input type="text" name="max_limit_grup_up" value="5M" style="width:71px"> / <input type="text" name="max_limit_grup_down" value="5M" style="width:72px"></td>
		</tr>
		<tr>
			<td>Limit At Grup</td>
			<td><input type="text" name="limit_at_grup_up" value="3M" style="width:71px"> / <input type="text" name="limit_at_grup_down" value="1M" style="width:72px"></td>
		</tr>
		<tr>
			<td>Max Limit Client</td>
			<td><input type="text" name="max_limit_parent_up" value="5M" style="width:71px"> / <input type="text" name="max_limit_parent_down" value="5M" style="width:72px"></td>
		</tr>
		<tr>
			<td>Limit At Client</td>
			<td><input type="text" name="limit_at_parent_up" value="512k" style="width:71px"> / <input type="text" name="limit_at_parent_down" value="1M" style="width:72px"></td>
		</tr>
		<tr>
			<td style="padding-top:10px"><input type="submit" value="Generate"></td>
	</table>
</form>
<code style="padding-left:12px; border-left:4px solid gray"><i>Powered by PHP. Development by <a href="https://www.instagram.com/muhammadfuadfachrudin/" style="color:black; text-decoration:none" target="_blank">Fuad567</a></i></code>
</div>

<?php
	if (@$_POST['grup_name']) {
?>

<p style="margin:25px 0px; color:#d70000">Output :</p>
<div style="padding-left:12px; border-left:4px solid gray">
/ip firewall mangle<br />
add chain=forward action=mark-packet new-packet-mark=koneksi-ping passthrough=no protocol=icmp log=no log-prefix="" comment="KONEKSI PING / ICMP"<br /><br />
/queue simple<br />
add name="PING / ICMP" target="" parent=none packet-marks=koneksi-ping priority=2/2 queue=default/default limit-at=512k/512k max-limit=1M/1M comment="PISAH TRAFFIC PING (PALING ATAS)"<br /><br />

<?php

	$custom_name = @$_POST['custom_name'];
	$grup_name = @$_POST['grup_name'];

	$network = @$_POST['network'];
	$start = @$_POST['start'];
	$end = @$_POST['end'];
	
	$max_limit_grup_up = @$_POST['max_limit_grup_up'];
	$max_limit_grup_down = @$_POST['max_limit_grup_down'];
	$limit_at_grup_up = @$_POST['limit_at_grup_up'];
	$limit_at_grup_down = @$_POST['limit_at_grup_down'];

	$max_limit_parent_up = @$_POST['max_limit_parent_up'];
	$max_limit_parent_down = @$_POST['max_limit_parent_down'];
	$limit_at_parent_up = @$_POST['limit_at_parent_up'];
	$limit_at_parent_down = @$_POST['limit_at_parent_down'];

	$client_per_grup = @$_POST['client_per_grup'];

	// Mulai program

	$total_grup = ($end - $start) / $client_per_grup;
	$ip = $start;
		
	for ($i = 1; $i <= $total_grup; $i++) {
		$mulai = $ip;
		$kelompok = $mulai + $client_per_grup;
		$comment_grup = '';
		$target_grup = '';
		
		for ($mulai; $mulai <= $kelompok; $mulai++) {
			$comment_grup .= $custom_name . ' ' . $mulai . ', ';
			$target_grup .= $network . '.' . $mulai . '/32,';
		}
		
		$comment_grup = substr($comment_grup, 0, -2);
		$target_grup = substr($target_grup, 0, -1);
		
		echo 'add name="'.$grup_name.'_'.$i.'" target='.$target_grup.' parent=none packet-marks="" priority=8/8 queue=pcq-upload-default/pcq-download-default limit-at='.$limit_at_grup_up.'/'.$limit_at_grup_down.' max-limit='.$max_limit_grup_up.'/'.$max_limit_grup_down.' comment="'.$max_limit_grup_down . ' MBPS GRUP ' . $i . ' : ' . $comment_grup.'"<br />';
		
		$sampai = $ip + $client_per_grup - 1;
		
		for ($ip; $ip <= $sampai; $ip++) {
			echo 'add name="CLIENT '.$ip.'" target='.$network.'.'.$ip.'/32 parent="'.$grup_name.'_'.$i.'" packet-marks="" priority=8/8 queue=pcq-upload-default/pcq-download-default limit-at='.$limit_at_parent_up.'/'.$limit_at_parent_down.' max-limit='.$max_limit_parent_up.'/'.$max_limit_parent_up.' comment="'.$custom_name . ' ' . $ip . '"<br />';
		}
		
		echo '<br />';
	}

}
?>

</div>
