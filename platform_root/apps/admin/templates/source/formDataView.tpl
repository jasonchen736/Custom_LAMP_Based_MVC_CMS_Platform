{include file="header.tpl"}

<table id="editTable">
	<tr>
		<td id="editMenuCell">
			<a href="{$lastQuery}" class="viewContent">Return to previous search</a>
			<br /><br />
			<ul id="editMenu">
				<li class="head">&nbsp;</li>
				<li class="editMenuOption selected">Form Data Details</li>
				<li class="end">&nbsp;</li>
			</ul>
		</td>
		<td id="editPropertyCell">
			<div class="propertyContainer">
				<table>
					<tr>
						<td><span class="normalLabel">Form Data ID:</span></td>
						<td>{$formData.formDataID}</td>
					</tr>
					<tr>
						<td><span class="normalLabel">Date:</span></td>
						<td>{$formData.date|date_format:"%m/%d/%Y %r"}</td>
					</tr>
					<tr>
						<td><span class="normalLabel">Type:</span></td>
						<td>{$formData.type}</td>
					</tr>
					<tr>
						<td><span class="normalLabel">First Name:</span></td>
						<td>{$formData.first}</td>
					</tr>
					<tr>
						<td><span class="normalLabel">Last Name:</span></td>
						<td>{$formData.last}</td>
					</tr>
					<tr>
						<td><span class="normalLabel">Email:</span></td>
						<td>{$formData.email}</td>
					</tr>
					<tr>
						<td><span class="normalLabel">Source:</span></td>
						<td>{$formData.source}</td>
					</tr>                    
{foreach from=$data key=field item=value}
					<tr>
						<td><span class="normalLabel">{$field}:</span></td>
						<td>{if is_array($value)}{implode(', ', $value)}{else}{$value}{/if}</td>
					</tr>
{/foreach}
				</table>
			</div>
		</td>
	</tr>
</table>

{include file="footer.tpl"}
