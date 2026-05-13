

document.getElementById('caricaDati')
.addEventListener('click', async () => {

  try {

    const response = await fetch('dati.json');

    if (!response.ok) {
      throw new Error(`Errore: ${response.status}`);
    }

    const dati = await response.json();

    document.getElementById('outputJson').textContent =
      JSON.stringify(dati, null, 2);

  } catch (error) {

    console.error(error);

    document.getElementById('outputJson').textContent =
      'Errore nel caricamento dei dati.';
  }

});
