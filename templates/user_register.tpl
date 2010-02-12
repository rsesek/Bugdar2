<h1>Hello World!</h1>

<form action="<?= EventLink('UserRegister') ?>" method="post">
<input type="hidden" name="do" value="submit" />

<dl>
    <dt>Email:</dt>
    <dd><input type="text" name="email" value="" id="email"></dd>

    <dt>Alias:</dt>
    <dd><input type="text" name="alias" value="" id="alias"></dd>

    <dt>Password:</dt>
    <dd><input type="password" name="password" value="" id="password"></dd>
</dl>

<p><input type="submit" value="Continue &rarr;"></p>
</form>