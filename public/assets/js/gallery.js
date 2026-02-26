(() => {
  const grid = document.querySelector('#gallery-grid[data-infinite="1"]');
  if (!grid) return;

  const apiUrl = grid.dataset.apiUrl;
  const loading = document.getElementById('loading');
  let page = 1;
  let isLoading = false;
  let done = false;

  const renderItem = (item) => {
    const card = document.createElement('article');
    card.className = 'card';
    card.innerHTML = item.media_type === 'video'
      ? `<video controls preload="metadata" src="${item.file_path}"></video>`
      : `<img loading="lazy" src="${item.file_path}" alt="${item.title}">`;

    const body = document.createElement('div');
    body.className = 'card-body';
    body.innerHTML = `<h3>${item.title}</h3><p>${item.description ?? ''}</p>`;
    card.append(body);
    return card;
  };

  const loadMore = async () => {
    if (isLoading || done || !apiUrl) return;
    isLoading = true;
    loading.style.display = 'block';

    try {
      const res = await fetch(`${apiUrl}?page=${page}&limit=12`);
      const payload = await res.json();
      payload.data.forEach((item) => grid.append(renderItem(item)));
      if (payload.next_page) {
        page = payload.next_page;
      } else {
        done = true;
        loading.textContent = 'No more media.';
      }
    } catch {
      loading.textContent = 'Failed to load media.';
      done = true;
    } finally {
      isLoading = false;
      if (!done) loading.style.display = 'none';
    }
  };

  const observer = new IntersectionObserver((entries) => {
    if (entries.some((e) => e.isIntersecting)) {
      loadMore();
    }
  }, { threshold: 0.2 });

  observer.observe(loading);
  loadMore();
})();
