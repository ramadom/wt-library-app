
// Main SPA components - book form, search and actions

const App = {

    data() {
        return {
            searchText: "",

            id: null,
            title: null,
            genre: null,
            author: null,
            publicationDate: null,
            ISBN: null,
            copies: null,

            books: [],
            bookGenres: [],

            errors: null //TODO implement error binding to form fields
        };
    },
    mounted() {
        // Load table data and options
        this.openAction();
        this.openGenreSelections();
    },
    methods: {

        // Action to load book table
        openAction() {
            getBooks(this.searchText).then(res => {
                this.books = res.data;
            });
        },

        // Edit button action
        editAction(book) {
            this.id = book.id;
            this.title = book.title;
            this.genre = book.genre;
            this.author = book.author;
            this.publicationDate = book.publicationDate;
            this.ISBN = book.ISBN;
            this.copies = book.copies;

            this.messages = null; // Reset messages

        },

        // Action to request book genre list
        openGenreSelections() {
            getBookGenres().then(res => {
                this.bookGenres = res.data;
            });
        },

        // New button action
        newAction() {
            this.resetForm();
        },

        // Save button action
        saveAction() {
            let book = {
                title: this.title,
                genre: this.genre,
                author: this.author,
                publicationDate: this.publicationDate,
                ISBN: this.ISBN,
                copies: Number(this.copies) //TODO to number conversion
            };
            // Check by ID if book is new or edited
            if (this.id != null) { // Check if .id null or undefined
                putBook(this.id, book)
                    .then(t => {
                        if (t.errors) { // On response errors
                            this.errors = t.errors;
                            this.showAlert(t.errors);
                        } else { // On response success
                            this.showSuccess(t.message);
                            this.resetForm();
                            this.openAction(); // Reload table
                        }
                    });
            } else {
                postBook(book)
                    .then(t => {
                        if (t.errors) {
                            this.errors = t.errors;
                            this.showAlert(t.errors);
                        } else {
                            this.showSuccess(t.message);
                            this.resetForm();
                            this.openAction(); // Reload table
                        }
                    });
            }
        },

        // Remove button action
        deleteAction(book) {
            deleteBook(book.id)
                .then((t) => {
                    this.showAlert(t.errors);
                    this.openAction();
                });
        },

        resetForm() {
            this.id = null;
            this.title = null;
            this.genre = null;
            this.author = null;
            this.publicationDate = null;
            this.ISBN = null;
            this.copies = null;
        },

        showAlert(errors) {
            if (errors) {
                this.$toast.open({message: JSON.stringify(errors), type: 'error'});
            }
        },
        showSuccess(messages) {
            if (messages) {
                this.$toast.open({message: JSON.stringify(messages), type: 'info'});
            }
        }
    }
};