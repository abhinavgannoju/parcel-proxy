document.getElementById('submitButton').addEventListener('click', function(event) {
    event.preventDefault();  // Prevent form submission so we can handle it with JS

    // Show loading animation
    document.getElementById('loading').style.display = 'block';

    // Start polling the server for status
    checkParcelStatus();
});

function checkParcelStatus() {
    // Get the parcel ID (it's passed from PHP, so use PHP echo to inject it into JS)
    var parcelId = <?php echo json_encode($pid); ?>;

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "check_status.php?pid=" + parcelId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);

            // If parcel is accepted, stop the loading animation and display the acceptor's details
            if (response.status === "Accepted") {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('acceptorDetails').style.display = 'block';
                document.getElementById('acceptorEmail').innerText = response.acceptorEmail;
            } else {
                // Keep polling every 5 seconds if not accepted
                setTimeout(checkParcelStatus, 5000);
            }
        }
    };
    xhr.send();
}
