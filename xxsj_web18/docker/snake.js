let score = 0;
let game_over = false;
let game_started = false;
let snake = [];
let food = {};
let direction = 'right';
let next_direction = 'right';
const grid_size = 20;
const tile_count = 20;
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const scoreboard = document.getElementById('scoreboard');
const message = document.getElementById('message');

function initGame() {
    score = 0;
    game_over = false;
    game_started = false;
    direction = 'right';
    next_direction = 'right';
    snake = [{ x: 10, y: 10 }];
    spawnFood();
    updateScoreboard();
    message.textContent = '\u6309\u4e0b\u4efb\u610f\u952e\u5f00\u59cb\u6e38\u620f';
}

function spawnFood() {
    food.x = Math.floor(Math.random() * tile_count);
    food.y = Math.floor(Math.random() * tile_count);
    for (let segment of snake) {
        if (segment.x === food.x && segment.y === food.y) { spawnFood(); break; }
    }
}

function updateScoreboard() {
    scoreboard.textContent = `\u5206\u6570: ${score}`;
}

function drawSnake() {
    ctx.fillStyle = '#4CAF50';
    for (let segment of snake) {
        ctx.fillRect(segment.x * grid_size, segment.y * grid_size, grid_size - 1, grid_size - 1);
    }
}

function drawFood() {
    ctx.fillStyle = '#FF5722';
    ctx.fillRect(food.x * grid_size, food.y * grid_size, grid_size - 1, grid_size - 1);
}

function moveSnake() {
    if (game_over || !game_started) return;
    direction = next_direction;
    const head = { ...snake[0] };
    switch (direction) {
        case 'up': head.y--; break;
        case 'down': head.y++; break;
        case 'left': head.x--; break;
        case 'right': head.x++; break;
    }
    if (head.x < 0 || head.x >= tile_count || head.y < 0 || head.y >= tile_count) {
        game_over = true;
        message.textContent = '\u6e38\u620f\u7ed3\u675f\uff01\u649e\u5230\u8fb9\u754c\u4e86';
        return;
    }
    for (let segment of snake) {
        if (segment.x === head.x && segment.y === head.y) {
            game_over = true;
            message.textContent = '\u6e38\u620f\u7ed3\u675f\uff01\u649e\u5230\u81ea\u5df1\u4e86';
            return;
        }
    }
    snake.unshift(head);
    if (head.x === food.x && head.y === food.y) {
        score += 5;
        updateScoreboard();
        spawnFood();
        if (score >= 30) {
            game_over = true;
            message.textContent = 'win\uff01\u6e38\u620f\u7ed3\u675f\uff08\u5df2\u8fbe\u6700\u9ad8\u5206\uff09';
            return;
        }
        checkClearCondition();
    } else { snake.pop(); }
}

function checkClearCondition() {
    if (score > 120 && !game_over) {
        (async () => {
            const s = await (await fetch('?v=1')).text();
            console.log("&#85;&#82;&#76;&#47;&#63;&#102;&#108;&#97;&#103;&#61;&#115;");
            console.log(s);
            message.textContent = "Reset:" + s;
        })();
        game_over = true;
        updateScoreboard();
    }
}

function run() {
    updateScoreboard();
    checkClearCondition();
    if (!game_started) {
        game_started = true;
        message.textContent = '';
        checkClearCondition();
    }
}

function gameLoop() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawSnake();
    drawFood();
    moveSnake();
    setTimeout(gameLoop, 200);
}

document.addEventListener('keydown', (e) => {
    if (!game_started) {
        game_started = true;
        message.textContent = '';
        return;
    }
    switch (e.key) {
        case 'ArrowUp': if (direction !== 'down') next_direction = 'up'; break;
        case 'ArrowDown': if (direction !== 'up') next_direction = 'down'; break;
        case 'ArrowLeft': if (direction !== 'right') next_direction = 'left'; break;
        case 'ArrowRight': if (direction !== 'left') next_direction = 'right'; break;
    }
});

initGame();
gameLoop();