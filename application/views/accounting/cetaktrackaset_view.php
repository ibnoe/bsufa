<?
$this->load->view(ADMIN_HEADER);
?>

<link rel="stylesheet" href="<?=base_url()?>assets/css/easyui.css" type="text/css" />
<link rel="stylesheet" href="<?=base_url()?>assets/css/icon.css" type="text/css" />
<!--link rel="stylesheet" href="<?=base_url()?>assets/css/demo.css" type="text/css" />

<!--script language="javascript" src="<?=base_url()?>assets/js/jquery-1.7.2.min.js"></script-->
<script language="javascript" src="<?=base_url()?>assets/js/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.edatagrid.js"></script>
<script language="javascript" src="<?=base_url()?>assets/js/calendar.js"></script>
<script language="javascript" src="<?=base_url()?>assets/js/calendar2.js"></script>

<script type="text/javascript">
$(function(){
            $.fn.datebox.defaults.formatter = function(date) {
                        var y = date.getFullYear();
                        var m = date.getMonth() + 1;
                        var d = date.getDate();
                        return (d < 10 ? '0' + d : d) + '-' + (m < 10 ? '0' + m : m) + '-' + y;
            };
		$('#tgl').datebox({  
        required:true  
    });      
 });
 
</script>
<h2><font color='red' size='4'>TRACKING ASSET <hr width="150px" align="left"></font></h2>
<form method="post" action="<?php echo base_url();?>akunting/cetaktrackaset_call/cetaktrack" target="blank">
<table>
	<tr>
		<td>Aset</td>
		<td> : </td>
		<td>
			<select>
				<option> Pilih Aset </option>
				<?php
				foreach ($asset as $row) {
					echo "<option value=".$row->kd_aset."> ".$row->nm_brg." </option>";
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td>All</td>
		<td> : </td>
		<td><input type="checkbox" name="all" id="all" value="1" style="width:120px"></td>
	</tr>
	<tr>
		<td>As Off</td>
		<td> : </td>
		<td><input type="text" name="tgl" id="tgl" class="required" style="width:120px"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="klik" id="klik" value="Print"/>
		<input type="submit" name="export" id="export" value="Export to Excel"/></td>
	</tr>
	
</table>
</form>

<?
$this->load->view(ADMIN_FOOTER);
?>
