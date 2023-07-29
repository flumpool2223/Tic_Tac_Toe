// ゲームの状態を取得する関数
function getGameState() {
  return fetch('action.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .catch(error => {
      console.error('エラー:', error);
    });
}

// ゲームの状態を更新する関数
function updateGameState(board) {
  var cells = document.getElementsByClassName('cell');
  for (var i = 0; i < cells.length; i++) {
    var row = Math.floor(i / 3);
    var col = i % 3;
    cells[i].innerHTML = board[row][col];
  }
}

// マスをクリックしたときの処理
function makeMove(row, col) {
  var data = { row: row, col: col };

  fetch('action.php', {
    method: 'POST',
    body: JSON.stringify(data),
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      if (data === undefined) {
        throw new Error('Received undefined data');
      }
      updateGameState(data.board);

      if (data.winner) {
        alert(data.winner + 'の勝ちです！');
      } else if (data.draw) {
        alert('引き分けです！');
      }
    })
    .catch(error => {
      console.error('エラー:', error);
    });
}

// ゲームをリセットする関数
function resetGame() {
  fetch('action.php', { method: 'POST', body: JSON.stringify({ reset: true }) })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      if (data === undefined) {
        throw new Error('Received undefined data');
      }
      updateGameState(data.board);
      window.location = 'index.html'; // リセット後にindex.htmlにリダイレクト
    })
    .catch(error => {
      console.error('エラー:', error);
    });
}

// 初期化
getGameState().then(data => {
  if (data === undefined) {
    throw new Error('Received undefined data');
  }
  updateGameState(data.board);
});
