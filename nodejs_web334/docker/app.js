const express = require('express');
const session = require('express-session');
const fs = require('fs');
const path = require('path');
const archiver = require('archiver');

const app = express();
const port = process.env.PORT || 3000;

const generatePassword = () => {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let pwd = '';
  for (let i = 0; i < 6; i += 1) {
    pwd += chars[Math.floor(Math.random() * chars.length)];
  }
  return pwd;
};

const setRandomPassword = () => {
  const password = generatePassword();
  const userFile = path.join(__dirname, 'www', 'user.js');
  const content = `module.exports = {\n  items: [\n    {username: 'CTF_flag{Please_to_login}', password: '${password}'}\n  ]\n};\n`;
  fs.writeFileSync(userFile, content, 'utf8');
  // eslint-disable-next-line no-console
  console.log(`Generated login password: ${password}`);
  return password;
};

setRandomPassword();
const loginRouter = require('./www/login');

app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(
  session({
    secret: process.env.SESSION_SECRET || 'dev-secret',
    resave: false,
    saveUninitialized: false,
    cookie: { maxAge: 20 * 60 * 1000 }
  })
);

app.use('/login', loginRouter);

app.get('/www.zip', (req, res) => {
  res.setHeader('Content-Type', 'application/zip');
  res.setHeader('Content-Disposition', 'attachment; filename="www.zip"');

  const archive = archiver('zip', { zlib: { level: 9 } });
  archive.on('error', (err) => {
    res.status(500).send({ error: err.message });
  });

  archive.pipe(res);
  archive.file(path.join(__dirname, 'www', 'login.js'), { name: 'login.js' });
  archive.file(path.join(__dirname, 'www', 'user.js'), { name: 'user.js' });
  archive.finalize();
});

const renderLoginPage = () => `<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登录</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 420px; margin: 40px auto; padding: 0 16px; }
    form { display: flex; flex-direction: column; gap: 12px; }
    input { padding: 10px; font-size: 14px; }
    button { padding: 10px; font-size: 14px; cursor: pointer; }
    .msg { margin-top: 12px; min-height: 20px; }
    .flag { font-weight: bold; color: #0a7; }
  </style>
</head>
<body>
  <h1>登录</h1>
  <form id="login-form">
    <input type="text" name="username" placeholder="用户名" required />
    <input type="password" name="password" placeholder="密码" required />
    <button type="submit">提交</button>
    <div class="msg" id="message"></div>
  </form>
  <script>
    const form = document.getElementById('login-form');
    const msg = document.getElementById('message');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      msg.textContent = '正在登录...';

      const formData = new URLSearchParams(new FormData(form));
      const res = await fetch('/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
      });

      const data = await res.json();
      if (data.ret_code === 0) {
        msg.innerHTML = '登录成功，Flag: <span class="flag">' + (data.ret_flag || 'FLAG_NOT_SET') + '</span>';
      } else {
        msg.textContent = data.ret_msg || '登录失败';
      }
    });
  </script>
</body>
</html>`;

const renderFlagPage = (username, flag) => `<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flag</title>
</head>
<body>
  <h1>欢迎，${username}</h1>
  <p>Flag: <strong>${flag}</strong></p>
  <a href="/">返回</a>
</body>
</html>`;

app.get('/', (req, res) => {
  res.type('html');
  if (req.session.loginUser) {
    const flag = process.env.GZCTF_FLAG || 'FLAG_NOT_SET';
    return res.send(renderFlagPage(req.session.loginUser, flag));
  }
  res.send(renderLoginPage());
});

app.listen(port, () => {
  // eslint-disable-next-line no-console
  console.log(`Listening on port ${port}`);
});
