document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchWrap = searchInput.parentElement;
    const tableBody = document.getElementById('studentTableBody');
    const allRows = Array.from(tableBody.querySelectorAll('tr'));

    const dropdown = document.createElement('div');
    dropdown.id = 'searchDropdown';
    Object.assign(dropdown.style, {
        position: 'absolute',
        top: 'calc(100% + 8px)',
        left: '0',
        width: '100%',
        background: '#fff',
        border: '1px solid #ddd',
        borderRadius: '16px',
        boxShadow: '0 4px 12px rgba(0,0,0,0.1)',
        zIndex: '999',
        display: 'none',
        overflow: 'hidden'
    });

    searchWrap.style.position = 'relative';
    searchWrap.appendChild(dropdown);

    function createItem(icon, name, studentId, course, section, query = '') {
        const item = document.createElement('div');
        item.style.cssText = 'display: flex; align-items: center; gap: 12px; padding: 10px 16px; cursor: pointer;';

        const iconEl = document.createElement('i');
        iconEl.className = `ti ti-${icon}`;
        iconEl.style.cssText = 'font-size: 15px; color: #aaa; flex-shrink: 0;';

        const info = document.createElement('div');
        info.style.cssText = 'display: flex; flex-direction: column;';

        const nameEl = document.createElement('span');
        nameEl.style.cssText = 'font-size: 14px; color: #222; font-weight: 500;';

        if (query) {
            const idx = name.toLowerCase().indexOf(query.toLowerCase());
            nameEl.innerHTML = idx !== -1
                ? name.slice(0, idx) + '<strong>' + name.slice(idx, idx + query.length) + '</strong>' + name.slice(idx + query.length)
                : name;
        } else {
            nameEl.textContent = name;
        }

        const sub = document.createElement('span');
        sub.textContent = `${studentId} • ${course} • ${section}`;
        sub.style.cssText = 'font-size: 12px; color: #aaa;';

        info.appendChild(nameEl);
        info.appendChild(sub);
        item.appendChild(iconEl);
        item.appendChild(info);

        item.addEventListener('mouseenter', () => item.style.background = '#f8f5f5');
        item.addEventListener('mouseleave', () => item.style.background = 'transparent');
        item.addEventListener('click', () => {
            searchInput.value = name;
            dropdown.style.display = 'none';
            allRows.forEach(r => {
                r.style.display = r.innerText.toLowerCase().includes(name.toLowerCase()) ? '' : 'none';
            });
        });

        return item;
    }

    function renderDropdown(query) {
        dropdown.innerHTML = '';
        allRows.forEach(row => row.style.display = '');

        if (query.length === 0) {
            const label = document.createElement('div');
            label.textContent = 'Recent students';
            label.style.cssText = 'padding: 10px 16px 4px; font-size: 11px; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px;';
            dropdown.appendChild(label);

            allRows.slice(0, 6).forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 0) return;
                dropdown.appendChild(createItem(
                    'clock',
                    cells[1].innerText,
                    cells[0].innerText,
                    cells[3].innerText,
                    cells[4].innerText
                ));
            });
            return;
        }

        const matched = allRows.filter(row =>
            row.innerText.toLowerCase().includes(query.toLowerCase())
        );

        allRows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(query.toLowerCase()) ? '' : 'none';
        });

        if (matched.length === 0) {
            const none = document.createElement('div');
            none.textContent = 'No students found';
            none.style.cssText = 'padding: 12px 16px; font-size: 14px; color: #aaa;';
            dropdown.appendChild(none);
            dropdown.style.display = 'block';
            return;
        }

        matched.slice(0, 6).forEach(row => {
            const cells = row.querySelectorAll('td');
            dropdown.appendChild(createItem(
                'user',
                cells[1].innerText,
                cells[0].innerText,
                cells[3].innerText,
                cells[4].innerText,
                query
            ));
        });

        dropdown.style.display = 'block';
    }

    searchInput.addEventListener('focus', () => {
        renderDropdown(searchInput.value.trim());
        dropdown.style.display = 'block';
    });

    let debounceTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => renderDropdown(searchInput.value.trim()), 150);
    });

    document.addEventListener('click', (e) => {
        if (!searchWrap.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
});