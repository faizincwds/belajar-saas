halaman admin

<script>
    Echo.channel('test-channel')
        .listen('TestEvent', (data) => {
            console.log('Pesan diterima:', data.message);
            alert('Pesan diterima: ' + data.message);
        });
</script>
