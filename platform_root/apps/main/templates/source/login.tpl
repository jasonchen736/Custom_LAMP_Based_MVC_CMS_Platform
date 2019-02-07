{include file="header.tpl"}

<div id="loginContainer">
	<div id="loginSubcontainer">
		<form action="/adminLogin" method="post" class="novalidate">
			<table>
				<tr>
					<td>
						<input type="text" name="login" value="{$login}" tabindex="1" />
					</td>
					<td rowspan="2">
						<input type="hidden" name="action" value="login" />
						<input class="button" type="submit" name="submit" value="Login" tabindex="3" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="password" name="pass" value="{$pass}" tabindex="2" fh:formhistory="off" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>

{include file="footer.tpl"}
