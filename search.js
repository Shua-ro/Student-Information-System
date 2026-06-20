document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const searchWrap = searchInput.parentElement;

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

    function goToSearch(query) {
        const params = new URLSearchParams(window.location.search);
        if (query) {
            params.set('search', query);
        } else {
            params.delete('search');
        }
        params.delete('page'); 
        const qs = params.toString();
        window.location.href = 'index.php' + (qs ? '?' + qs : '');
    }

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
            
            dropdown.style.display = 'none';
            goToSearch(studentId);
        });

        return item;
    }

    async function fetchMatches(query) {
        try {
            const res = await fetch('searching.php?q=' + encodeURIComponent(query));
            if (!res.ok) return [];
            return await res.json();
        } catch (err) {
            console.error('Search request failed:', err);
            return [];
        }
    }

    async function renderDropdown(query) {
        dropdown.innerHTML = '';

        const matches = await fetchMatches(query);

        if (query.length === 0) {
            const label = document.createElement('div');
            label.textContent = 'Recent students';
            label.style.cssText = 'padding: 10px 16px 4px; font-size: 11px; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px;';
            dropdown.appendChild(label);
        }

        if (matches.length === 0) {
            const none = document.createElement('div');
            none.textContent = 'No students found';
            none.style.cssText = 'padding: 12px 16px; font-size: 14px; color: #aaa;';
            dropdown.appendChild(none);
            dropdown.style.display = 'block';
            return;
        }

        matches.forEach(s => {
            dropdown.appendChild(createItem(
                query.length === 0 ? 'clock' : 'user',
                `${s.first_name} ${s.last_name}`,
                s.student_id,
                s.course,
                s.section,
                query
            ));
        });

        dropdown.style.display = 'block';
    }

    searchInput.addEventListener('focus', () => {
        renderDropdown(searchInput.value.trim());
    });

    let debounceTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => renderDropdown(searchInput.value.trim()), 200);
    });

    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            dropdown.style.display = 'none';
            goToSearch(searchInput.value.trim());
        });
    }

    document.addEventListener('click', (e) => {
        if (!searchWrap.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
});
