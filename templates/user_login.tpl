<h1>Login</h1>

<form action="<?= EventLink('UserLogin') ?>" method="post">
<input type="hidden" name="do" value="fire" />
<input type="hidden" name="last_event" value="$[last_event]" />

<dl>
    <dt>Email:</dt>
    <dd><input type="text" name="email" value="" id="email"></dd>

    <dt>Password:</dt>
    <dd><input type="password" name="password" value="" id="password"></dd>
</dl>

<p><input type="submit" value="Continue &rarr;"></p>
</form>