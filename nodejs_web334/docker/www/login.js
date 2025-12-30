const express = require('express');
const router = express.Router();
const users = require('./user').items;

router.post('/', (req, res) => {
    res.type('json');
    const flag = process.env.GZCTF_FLAG || 'FLAG_NOT_SET';
    const name = (req.body.username || '').trim();
    const password = req.body.password || '';

    const user = users.find((item) => {
        const lower = item.username.toLowerCase();
        return name !== item.username && name.toLowerCase() === lower && item.password === password;
    });

    if (user) {
        req.session.regenerate((err) => {
            if (err) {
                return res.json({ ret_code: 2, ret_msg: '登录失败' });
            }

            req.session.loginUser = user.username;
            res.json({ ret_code: 0, ret_msg: '登录成功', ret_flag: flag });
        });
    } else {
        res.json({ ret_code: 1, ret_msg: '账号或密码错误' });
    }
});

module.exports = router;