
/* =============================================================
   1. UTILITY HELPERS
   ============================================================= */

/**
 * Show an error message under a field.
 * @param {string} inputId  - the id of the input element
 * @param {string} errorId  - the id of the <span class="error-msg">
 * @param {string} message  - message to show
 */
function showError(inputId, errorId, message) {
  const input = document.getElementById(inputId);
  const errorEl = document.getElementById(errorId);
  if (input)  input.classList.add('error');
  if (errorEl) {
    errorEl.textContent = message;
    errorEl.classList.add('visible');
  }
}

/**
 * Clear an error message from a field.
 */
function clearError(inputId, errorId) {
  const input = document.getElementById(inputId);
  const errorEl = document.getElementById(errorId);
  if (input)  input.classList.remove('error');
  if (errorEl) errorEl.classList.remove('visible');
}

/**
 * Check if a string is empty or only whitespace.
 */
function isEmpty(value) {
  return value === null || value === undefined || value.trim() === '';
}

/**
 * Validate an ISBN-13 format loosely.
 * Accepts both 978-X-XX-XXXXXX-X and plain digits.
 */
function isValidISBN(value) {
  // Strip hyphens and spaces, check 13 digits
  const stripped = value.replace(/[-\s]/g, '');
  return /^\d{13}$/.test(stripped) || /^\d{10}$/.test(stripped);
}


/* =============================================================
   2. LOGIN FORM VALIDATION
   ============================================================= */

const loginForm = document.getElementById('loginForm');

if (loginForm) {

  // Clear errors on input (real-time feedback)
  document.getElementById('username').addEventListener('input', function () {
    if (!isEmpty(this.value)) clearError('username', 'usernameError');
  });

  document.getElementById('password').addEventListener('input', function () {
    if (!isEmpty(this.value)) clearError('password', 'passwordError');
  });

  loginForm.addEventListener('submit', function (e) {
    let valid = true;

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    if (isEmpty(username)) {
      showError('username', 'usernameError', 'Username is required.');
      valid = false;
    }

    if (isEmpty(password)) {
      showError('password', 'passwordError', 'Password is required.');
      valid = false;
    } else if (password.length < 4) {
      showError('password', 'passwordError', 'Password must be at least 4 characters.');
      valid = false;
    }

    // If validation fails, prevent form from submitting to PHP
    if (!valid) {
      e.preventDefault();
      console.log('[Validation] Login form failed client-side validation.');
    } else {
      console.log('[Validation] Login form passed. Submitting to PHP...');
      // PHP (login.php) will handle the actual credential check
    }
  });
}


/* =============================================================
   3. ADD BOOK FORM VALIDATION
   ============================================================= */

const addBookForm = document.getElementById('addBookForm');

if (addBookForm) {
  addBookForm.addEventListener('submit', function (e) {
    let valid = true;

    // ISBN
    const isbn = document.getElementById('isbn').value;
    if (isEmpty(isbn)) {
      showError('isbn', 'isbnError', 'ISBN is required.');
      valid = false;
    } else if (!isValidISBN(isbn)) {
      showError('isbn', 'isbnError', 'Enter a valid ISBN (10 or 13 digits).');
      valid = false;
    } else {
      clearError('isbn', 'isbnError');
    }

    // Title
    const title = document.getElementById('title').value;
    if (isEmpty(title)) {
      showError('title', 'titleError', 'Book title is required.');
      valid = false;
    } else {
      clearError('title', 'titleError');
    }

    // Author
    const author = document.getElementById('author').value;
    if (isEmpty(author)) {
      showError('author', 'authorError', 'Author name is required.');
      valid = false;
    } else {
      clearError('author', 'authorError');
    }

    // Price
    const price = parseFloat(document.getElementById('price').value);
    if (isNaN(price) || price <= 0) {
      showError('price', 'priceError', 'Enter a valid price greater than 0.');
      valid = false;
    } else {
      clearError('price', 'priceError');
    }

    // Stock
    const stock = parseInt(document.getElementById('stock').value);
    if (isNaN(stock) || stock < 0) {
      showError('stock', 'stockError', 'Stock quantity must be 0 or more.');
      valid = false;
    } else {
      clearError('stock', 'stockError');
    }

    if (!valid) {
      e.preventDefault();
      console.log('[Validation] Add Book form failed validation.');
    }
  });
}


/* =============================================================
   4. REQUISITION FORM VALIDATION
   ============================================================= */

const requisitionForm = document.getElementById('requisitionForm');

if (requisitionForm) {
  requisitionForm.addEventListener('submit', function (e) {

    // Check that at least one quantity input has a value > 0
    const qtyInputs = document.querySelectorAll('.qty-input');
    let hasQty = false;

    qtyInputs.forEach(function (input) {
      const val = parseInt(input.value);
      if (!isNaN(val) && val > 0) {
        hasQty = true;
      }

      // Validate each qty doesn't exceed max (stock)
      const max = parseInt(input.getAttribute('max'));
      if (!isNaN(val) && val > max) {
        input.classList.add('error');
        alert('Requested quantity for one or more books exceeds available stock. Please adjust.');
        e.preventDefault();
      }
    });

    if (!hasQty) {
      e.preventDefault();
      alert('Please enter a quantity for at least one book before submitting your requisition.');
      return;
    }

    console.log('[Validation] Requisition form passed. Submitting to PHP...');
  });
}


/* =============================================================
   5. LIVE TABLE SEARCH / FILTER  (DOM MANIPULATION)
   Searches by title, author, or ISBN — highlights matches
   ============================================================= */

/**
 * Wire up a search input to filter rows in a table.
 * @param {string} searchInputId - id of the <input type="text"> search box
 * @param {string} tableId       - id of the <table> to filter
 */
function wireTableSearch(searchInputId, tableId) {
  const searchInput = document.getElementById(searchInputId);
  const table = document.getElementById(tableId);

  if (!searchInput || !table) return;

  searchInput.addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(function (row) {
      // Get all text content of the row
      const text = row.textContent.toLowerCase();

      if (query === '' || text.includes(query)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
}

// Wire up both tables
wireTableSearch('searchInput', 'catalogTable');
wireTableSearch('adminSearch', 'adminTable');


/* =============================================================
   6. MODAL OPEN / CLOSE   (DOM MANIPULATION)
   ============================================================= */

/**
 * Open a modal by its id.
 * @param {string} modalId
 */
function openModal(modalId) {
  const overlay = document.getElementById(modalId);
  if (overlay) {
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden'; // Prevent background scroll
  }
}

/**
 * Close a modal by its id.
 * @param {string} modalId
 */
function closeModal(modalId) {
  const overlay = document.getElementById(modalId);
  if (overlay) {
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }
}

// Close modal when clicking the dark overlay (not the card itself)
document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) {
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }
  });
});

// Close modal on ESC key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.open').forEach(function (overlay) {
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    });
  }
});


/* =============================================================
   7. SELECT-ALL CHECKBOX   (DOM MANIPULATION)
   ============================================================= */

const selectAll = document.getElementById('selectAll');

if (selectAll) {
  selectAll.addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('input[name="selected_books[]"]');
    checkboxes.forEach(function (cb) {
      cb.checked = selectAll.checked;
    });
  });
}


/* ========================================================
   8. DELETE CONFIRMATION — Populate modal dynamically
   Called from table row: onclick="confirmDelete(id, title)"
   ============================================================= */

/**
 * Open the delete confirmation modal and populate it with the
 * book's id and name, so the form submits the correct record.
 * @param {number} bookId
 * @param {string} bookName
 */
function confirmDelete(bookId, bookName) {
  document.getElementById('delete_book_id').value = bookId;
  document.getElementById('deleteBookName').textContent = '"' + bookName + '"';
  openModal('deleteModal');
}


/* =============================================================
   9. FLASH MESSAGE AUTO-DISMISS   (DOM MANIPULATION)
   Any .alert with class .auto-dismiss fades out after 4 seconds
   ============================================================= */

document.querySelectorAll('.alert.auto-dismiss').forEach(function (alert) {
  setTimeout(function () {
    alert.style.transition = 'opacity 0.5s ease';
    alert.style.opacity = '0';
    setTimeout(function () { alert.style.display = 'none'; }, 500);
  }, 4000);
});


/* =============================================================
   10. QTY INPUT — Highlight row when quantity is entered
   Visual feedback: row gets a subtle blue tint when active
   ============================================================= */

document.querySelectorAll('.qty-input').forEach(function (input) {
  input.addEventListener('input', function () {
    const row = this.closest('tr');
    if (this.value && parseInt(this.value) > 0) {
      row.style.background = 'var(--blue-xlight)';
    } else {
      row.style.background = '';
    }
  });
});

console.log('[Decorum B2B] validation.js loaded successfully.');
