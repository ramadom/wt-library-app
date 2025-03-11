
    // API requests

      function getBooks(searchText) {
        let search = searchText ? "?search=" + searchText.trim() : "";
        return fetch('/api/book' + search, {
          method: 'GET',
          headers: {
            'content-type': 'application/json'
          }
        }).then(response => response.json());
      }

      function getBook(id) {
        return fetch('/api/book/' + id, {
          method: 'GET',
          headers: {
            'content-type': 'application/json'
          }
        });
      }

      function postBook(book) {
        return fetch('/api/book/', {
          method: 'POST',
          body: JSON.stringify(book),
          headers: {
            'content-type': 'application/json'
          }
        }).then(response => response.json());
      }

      function putBook(id, book) {
        return fetch('/api/book/' + id, {
          method: 'PUT',
          body: JSON.stringify(book),
          headers: {
            'content-type': 'application/json'
          }
        }).then(response => response.json());
      }

      function deleteBook(id) {
        return fetch('/api/book/' + id, {
          method: 'DELETE',
          headers: {
            'content-type': 'application/json'
          }
        }).then(response => response.json());
      }

      function getBookGenres() {
        return fetch('/api/book/genre', {
          method: 'GET',
          headers: {
            'content-type': 'application/json'
          }
        }).then(response => response.json());
      }
