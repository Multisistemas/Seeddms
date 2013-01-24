<?php
/**
 * Implementation of WorkspaceMgr view
 *
 * @category   DMS
 * @package    LetoDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for WorkspaceMgr view
 *
 * @category   DMS
 * @package    LetoDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class LetoDMS_View_WorkflowMgr extends LetoDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$selworkflow = $this->params['selworkflow'];
		$workflows = $this->params['allworkflows'];
		$workflowstates = $this->params['allworkflowstates'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

?>
<script language="JavaScript">

function checkForm(num)
{
	msg = "";
	eval("var formObj = document.form" + num + ";");

	if (formObj.login.value == "") msg += "<?php printMLText("js_no_login");?>\n";
	if ((num == '0') && (formObj.pwd.value == "")) msg += "<?php printMLText("js_no_pwd");?>\n";
	if ((formObj.pwd.value != formObj.pwdconf.value)&&(formObj.pwd.value != "" )&&(formObj.pwd.value != "" )) msg += "<?php printMLText("js_pwd_not_conf");?>\n";
	if (formObj.name.value == "") msg += "<?php printMLText("js_no_name");?>\n";
	if (formObj.email.value == "") msg += "<?php printMLText("js_no_email");?>\n";
	//if (formObj.comment.value == "") msg += "<?php printMLText("js_no_comment");?>\n";
	if (msg != "")
	{
		alert(msg);
		return false;
	}
	else
		return true;
}


obj = -1;
function showWorkflow(selectObj) {
	if (obj != -1) {
		obj.style.display = "none";
	}

	id = selectObj.options[selectObj.selectedIndex].value;
	if (id > 0) {
		$('#workflowgraph').show();
		$('#workflowgraph iframe').attr('src', 'out.WorkflowGraph.php?workflow='+id);
	} else {
		$('#workflowgraph').hide();
	}

	if (id == -1)
		return;

	obj = document.getElementById("keywords" + id);
	obj.style.display = "";

}
</script>
<?php
		$this->contentHeading(getMLText("workflow_management"));
?>

<div class="row-fluid">
<div class="span4">
<div class="well">
<?php echo getMLText("selection")?>:
<select onchange="showWorkflow(this)" id="selector">
<option value="-1"><?php echo getMLText("choose_workflow")?>
<option value="0"><?php echo getMLText("add_workflow")?>
<?php
		$selected=0;
		$count=2;
		foreach ($workflows as $currWorkflow) {
			if (isset($selworkflow) && $currWorkflow->getID()==$selworkflow->getID()) $selected=$count;
			print "<option value=\"".$currWorkflow->getID()."\">" . htmlspecialchars($currWorkflow->getName());
			$count++;
		}
?>
</select>
</div>
<div id="workflowgraph" class="well">
<iframe src="out.WorkflowGraph.php?workflow=1" width="100%" height="500" style="border: 1px solid #AAA;"></iframe>
</div>
</div>

<div class="span8">
<div class="well">
<table class="table-condensed">
	<tr>
	<td id="keywords0" style="display : none;">

	<form action="../op/op.WorkflowMgr.php" method="post" enctype="multipart/form-data" name="form0" onsubmit="return checkForm('0');">
  <?php echo createHiddenFieldWithKey('addworkflow'); ?>
	<input type="Hidden" name="action" value="addworkflow">
	<table class="table-condensed">
		<tr>
			<td><?php printMLText("workflow_name");?>:</td>
			<td><input type="text" name="name"></td>
		</tr>
		<tr>
			<td><?php printMLText("workflow_initstate");?>:</td>
			<td><select name="initstate">
<?php
		foreach($workflowstates as $workflowstate) {
			echo "<option value=\"".$workflowstate->getID()."\"";
			echo ">".htmlspecialchars($workflowstate->getName())."</option>\n";
		}
?>
			</select></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" class="btn" value="<?php printMLText("add_workflow");?>"></td>
		</tr>
	</table>
	</form>
	</td>

<?php
		foreach ($workflows as $currWorkflow) {

			print "<td id=\"keywords".$currWorkflow->getID()."\" style=\"display : none;\">";
?>
	<form action="../op/op.WorkflowMgr.php" method="post" enctype="multipart/form-data" name="form<?php print $currWorkflow->getID();?>" onsubmit="return checkForm('<?php print $currWorkflow->getID();?>');">
	<?php echo createHiddenFieldWithKey('editworkflow'); ?>
	<input type="Hidden" name="workflowid" value="<?php print $currWorkflow->getID();?>">
	<input type="Hidden" name="action" value="editworkflow">
	<table class="table-condensed">
		<tr>
			<td></td>
			<td>
<?php
			if($currWorkflow->isUsed()) {
?>
				<p><?php echo getMLText('workflow_in_use') ?></p>
<?php
			} else {
?>
			  <a class="standardText btn" href="../out/out.RemoveWorkflow.php?workflowid=<?php print $currWorkflow->getID();?>"><i class="icon-remove"></i> <?php printMLText("rm_workflow");?></a>
<?php
			}
?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("workflow_name");?>:</td>
			<td><input type="text" name="name" value="<?php print htmlspecialchars($currWorkflow->getName());?>"></td>
		</tr>
		<tr>
			<td><?php printMLText("workflow_initstate");?>:</td>
			<td><select name="initstate">
<?php
		foreach($workflowstates as $workflowstate) {
			echo "<option value=\"".$workflowstate->getID()."\"";
			if($currWorkflow->getInitState()->getID() == $workflowstate->getID())
				echo " selected=\"selected\"";
			echo ">".htmlspecialchars($workflowstate->getName())."</option>\n";
		}
?>
			</select></td>
		</tr>

		<tr>
			<td></td>
			<td><input type="submit" class="btn" value="<?php printMLText("save");?>"></td>
		</tr>
	</table>
	</form>
<?php
		$transitions = $currWorkflow->getTransitions();
		if($transitions) {
			echo "<table class=\"table table-condensed\">";
			echo "<tr><th>State</th><th>Action</th><th>Next state</th><th>".getMLText('user')."/".getMLText('group')."</th><th>Document status</th></tr>";
			foreach($transitions as $transition) {
				$state = $transition->getState();
				$nextstate = $transition->getNextState();
				$action = $transition->getAction();
				echo "<tr><td>".$state->getName()."</td><td>".$action->getName()."</td><td>".$nextstate->getName()."</td>";
				echo "<td>";
				$transusers = $transition->getUsers();
				foreach($transusers as $transuser) {
					$u = $transuser->getUser();
					echo "User ".$u->getFullName();
					echo "<br />";
				}
				$transgroups = $transition->getGroups();
				foreach($transgroups as $transgroup) {
					$g = $transgroup->getGroup();
					echo "At least ".$transgroup->getNumOfUsers()." users of ".$g->getName();
					echo "<br />";
				}
				echo "</td>";
				$docstatus = $nextstate->getDocumentStatus();
				if($docstatus == S_RELEASED || $docstatus == S_REJECTED) {
					echo "<td>".getOverallStatusText($docstatus)."</td>";
				} else {
					echo "<td></td>";
				}
				echo "<td>";
?>
<form class="form-inline" action="../op/op.RemoveTransitionFromWorkflow.php" method="post">
  <?php echo createHiddenFieldWithKey('removetransitionfromworkflow'); ?>
	<input type="hidden" name="workflow" value="<?php print $currWorkflow->getID();?>">
	<input type="hidden" name="transition" value="<?php print $transition->getID(); ?>">
	<button type="submit" class="btn"><i class="icon-remove"></i> <?php printMLText("delete");?></button>
</form>
<?php
				echo "</td>";
				echo "</tr>\n";
			}
?>
<form class="form-inline" action="../op/op.AddTransitionToWorkflow.php" method="post">
<?php
			echo "<tr>";
			echo "<td>";
			echo "<select name=\"state\">";
			$states = $dms->getAllWorkflowStates();
			foreach($states as $state) {
				echo "<option value=\"".$state->getID()."\">".$state->getName()."</option>";
			}
			echo "</select>";
			echo "</td>";
			echo "<td>";
			echo "<select name=\"action\">";
			$actions = $dms->getAllWorkflowActions();
			foreach($actions as $action) {
				echo "<option value=\"".$action->getID()."\">".$action->getName()."</option>";
			}
			echo "</select>";
			echo "</td>";
			echo "<td>";
			echo "<select name=\"nextstate\">";
			$states = $dms->getAllWorkflowStates();
			foreach($states as $state) {
				echo "<option value=\"".$state->getID()."\">".$state->getName()."</option>";
			}
			echo "</select>";
			echo "</td>";
			echo "<td>";
      echo "<select class=\"chzn-select\" name=\"users[]\" multiple=\"multiple\" data-placeholder=\"".getMLText('select_ind_reviewers')."\">";
			$allusers = $dms->getAllUsers();
			foreach($allusers as $usr) {
				print "<option value=\"".$usr->getID()."\">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
			}
			echo "</select>";
			echo "<br />";
      echo "<select class=\"chzn-select\" name=\"groups[]\" multiple=\"multiple\" data-placeholder=\"".getMLText('select_ind_reviewers')."\">";
			$allgroups = $dms->getAllGroups();
			foreach($allgroups as $grp) {
				print "<option value=\"".$grp->getID()."\">". htmlspecialchars($grp->getName())."</option>";
			}
			echo "</select>";
			echo "</td>";
			echo "<td>";
			echo "<select name=\"documenstatus\">";
			echo "<option value=\"\">"."</option>";
			echo "<option value=\"".S_RELEASED."\">".getMLText('released')."</option>";
			echo "<option value=\"".S_REJECTED."\">".getMLText('rejected')."</option>";
			echo "</select>";
			echo "</td>";
			echo "<td>";
?>
  <?php echo createHiddenFieldWithKey('addtransitiontoworkflow'); ?>
	<input type="hidden" name="workflow" value="<?php print $currWorkflow->getID();?>">
	<input type="submit" class="btn" value="<?php printMLText("add");?>">
<?php
			echo "</td>";
			echo "</tr>\n";
?>
</form>
<?php
			echo "</table>";
		}
?>
</td>
<?php  } ?>
</tr></table>
</div>
</div>
</div>

<script language="JavaScript">

sel = document.getElementById("selector");
sel.selectedIndex=<?php print $selected ?>;
showWorkflow(sel);

</script>


<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>