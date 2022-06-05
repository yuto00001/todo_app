'use strict';

{
  const token = document.querySelector('main').dataset.token;
  const input = document.querySelector('[name="title"]');
  const ul = document.querySelector('ul');


  // クリックされた要素は e の target で取得できる
  // checkbox の場合は type プロパティを調べてあげて、その値が checkbox かどうか。
  // 削除のほうはクリックされた要素に delete クラスが付いているかどうかで、判定できる。
  ul.addEventListener('click', e => {
    if (e.target.type === 'checkbox') {
      fetch('?action=toggle', {
        method: 'POST',
        body: new URLSearchParams({
          id: e.target.parentNode.dataset.id,
          token: token,
        }),
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('This todo has been deleted!');
        }
          return response.json();
      })
      .then(json => {
        if (json.is_done !== e.target.checked) {
          alert('This Todo has been updated. UI is being updated.');
          e.target.checked = json.is_done;
        }
      })
      .catch(err => {
        alert(err.message);
        location.reload();
      });
    }

    // ! をつけなければ、okを押して削除する反対の処理が可能。
    if (e.target.classList.contains('delete')) {
      if (!confirm('Are you sure?')) {
        return;
      }
      fetch('?action=delete', {
        method: 'POST',
        body: new URLSearchParams({
          id: e.target.parentNode.dataset.id,
          token: token,
        }),
      });
      e.target.parentNode.remove();
    }
  });

  input.focus();

  function addTodo(id, titleValue) {
  //   <li data-id="">
  //   <input type="checkbox">
  //   <span></span>
  //   <span class="delete">x</span>
  // </li>
  // 上記の構造を DOM 操作で作る。
    const li = document.createElement('li');
    li.dataset.id = id;
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    const title = document.createElement('span');
    title.textContent = titleValue;
    const deleteSpan = document.createElement('span');
    deleteSpan.textContent = 'x';
    deleteSpan.classList.add('delete');

    // liを組み立て、insertBeforeを使って新しいtodoは一番上に追加されるようにする。
    li.appendChild(checkbox);
    li.appendChild(title);
    li.appendChild(deleteSpan);

    ul.insertBefore(li, ul.firstChild);
  }

  document.querySelector('form').addEventListener('submit', e => {
    e.preventDefault();
    const title = input.value;

    // 非同期通信の値の取り出し
    fetch('?action=add', {
      method: 'POST',
      body: new URLSearchParams({
        title: title,
        token: token,
      }),
    })
    .then(response => response.json())
    .then(json => {
      addTodo(json.id, title);
    });
    // formの見た目を空にして、focusも当てておく。
    input.value = '';
    input.focus();
  });


  const purge = document.querySelector('.purge');
  purge.addEventListener('click', () => {
    // ! をつけなければ、okを押して削除する反対の処理が可能。
    if (!confirm('Are you sure?')) {
      return;
    }

    fetch('?action=purge', {
      method: 'POST',
      body: new URLSearchParams({
        token: token,
      }),
    });

    const lis = document.querySelectorAll('li');
    // forEach() メソッドは、与えられた関数を、配列の各要素に対して一度ずつ実行する。
    lis.forEach(li => {
      // li の最初の子要素、つまり children の 0 の checked プロパティを調べて、それが true だったら li を remove する。
      if (li.children[0].checked) {
        li.remove();
      }
    });
  });
}
