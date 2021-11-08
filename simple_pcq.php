/ip firewall mangle<br />
add chain=forward action=mark-packet new-packet-mark=koneksi-ping passthrough=no protocol=icmp log=no log-prefix="" comment="KONEKSI PING / ICMP"<br /><br />
/queue simple<br />
add name="PING / ICMP" target="" parent=none packet-marks=koneksi-ping priority=2/2 queue=default/default limit-at=512k/512k max-limit=1M/1M comment="PISAH TRAFFIC PING (PALING ATAS)"<br /><br />

<?php

	$custom_name = 'CLIENT';
	$grup_name = '5 Mbps';

	$network = '192.168.102';
	$start = 2;
	$end = 254;
	
	$max_limit_grup_up = '5M';
	$max_limit_grup_down = '5M';
	$limit_at_grup_up = '3M';
	$limit_at_grup_down = '5M';

	$max_limit_parent_up = '5M';
	$max_limit_parent_down = '5M';
	$limit_at_parent_up = '512k';
	$limit_at_parent_down = '1M';

	$client_per_grup = '5';

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
		
		// echo $comment_grup;
		// echo $i.'<br />';
		echo 'add name="'.$grup_name.'_'.$i.'" target='.$target_grup.' parent=none packet-marks="" priority=8/8 queue=pcq-upload-default/pcq-download-default limit-at='.$limit_at_grup_up.'/'.$limit_at_grup_down.' max-limit='.$max_limit_grup_up.'/'.$max_limit_grup_down.' comment="'.$max_limit_grup_down . ' MBPS GRUP ' . $i . ' : ' . $comment_grup.'"<br />';
		
		$sampai = $ip + $client_per_grup - 1;
		
		for ($ip; $ip <= $sampai; $ip++) {
			echo 'add name="CLIENT '.$ip.'" target='.$network.'.'.$ip.'/32 parent="'.$grup_name.'_'.$i.'" packet-marks="" priority=8/8 queue=pcq-upload-default/pcq-download-default limit-at='.$limit_at_parent_up.'/'.$limit_at_parent_up.' max-limit='.$max_limit_parent_up.'/'.$max_limit_parent_up.' comment="'.$custom_name . ' ' . $ip . '"<br />';
		}
		
		echo '<br />';
	}
		
?>
