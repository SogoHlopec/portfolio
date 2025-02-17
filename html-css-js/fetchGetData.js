const fetchGetData = async (requestData) => {
    try {
        const response = await fetch(AJAX_HANDLER_URL, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({
                request: requestData,
            }),
        });

        if (!response.ok) {
            console.log(`Error: ${response.status} ${response.message}`);
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.log(error);
    }
};
