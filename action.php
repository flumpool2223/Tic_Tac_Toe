<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();

// ゲームの状態を管理するセッション変数を初期化する
if (!isset($_SESSION['board'])) {
  $_SESSION['board'] = [
    ['', '', ''],
    ['', '', ''],
    ['', '', '']
  ];
}

// マスをクリックしたときの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);

  // リセットボタンが押された場合
  if (isset($data['reset']) && $data['reset'] === true) {
    resetGame();
    echo json_encode(['board' => $_SESSION['board']]);
    exit;
  }

  $row = $data['row'];
  $col = $data['col'];

  // すでにマスが埋まっている場合は何もしない
  if ($_SESSION['board'][$row][$col] !== '') {
    echo json_encode(['board' => $_SESSION['board']]);
    exit;
  }

  // マスを現在のプレイヤーで埋める
  $_SESSION['board'][$row][$col] = $_SESSION['currentPlayer'];

  // 勝敗判定
  if (checkWin($_SESSION['currentPlayer'])) {
    $winner = $_SESSION['currentPlayer'];
    resetGame();
    echo json_encode(['board' => $_SESSION['board'], 'winner' => $winner]);
    exit;
  } elseif (checkDraw()) {
    resetGame();
    echo json_encode(['board' => $_SESSION['board'], 'draw' => true]);
    exit;
  } else {
    // プレイヤーを切り替える
    $_SESSION['currentPlayer'] = ($_SESSION['currentPlayer'] === '○') ? '×' : '○';
    echo json_encode(['board' => $_SESSION['board']]);
    exit;
  }
}

// 勝敗を判定する関数
function checkWin($player) {
  $board = $_SESSION['board'];

  // 横方向のチェック
  for ($row = 0; $row < 3; $row++) {
    if (
      $board[$row][0] === $player &&
      $board[$row][1] === $player &&
      $board[$row][2] === $player
    ) {
      return true;
    }
  }

  // 縦方向のチェック
  for ($col = 0; $col < 3; $col++) {
    if (
      $board[0][$col] === $player &&
      $board[1][$col] === $player &&
      $board[2][$col] === $player
    ) {
      return true;
    }
  }

  // 対角線方向のチェック
  if (
    $board[0][0] === $player &&
    $board[1][1] === $player &&
    $board[2][2] === $player
  ) {
    return true;
  }

  if (
    $board[0][2] === $player &&
    $board[1][1] === $player &&
    $board[2][0] === $player
  ) {
    return true;
  }

  return false;
}

// 引き分けを判定する関数
function checkDraw() {
  $board = $_SESSION['board'];

  for ($row = 0; $row < 3; $row++) {
    for ($col = 0; $col < 3; $col++) {
      if ($board[$row][$col] === '') {
        return false;
      }
    }
  }
  return true;
}

// ゲームをリセットする関数
function resetGame() {
  $_SESSION['board'] = [
    ['', '', ''],
    ['', '', ''],
    ['', '', '']
  ];
  $_SESSION['currentPlayer'] = '○';
}
