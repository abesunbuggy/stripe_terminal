// Establish WebSocket connection
const socket = new WebSocket("ws://localhost:8080/websocket");

// Event listener when the WebSocket connection is opened
socket.onopen = function (event) {
  console.log("WebSocket connection opened");
};

// Event listener when the WebSocket receives a message
socket.onmessage = function (event) {
  // Parse the received data (assuming it's in JSON format)
  const eventData = JSON.parse(event.data);

  // Handle log messages from the server
  if (eventData.type === "log_message") {
    console.log(`Server Log: ${eventData.data}`);
  } else if (eventData.type === "payment_status_update") {
    // Update the page content based on the payment status
    const statusDiv = document.getElementById("status");
    statusDiv.innerHTML = `Payment Status: ${eventData.data.status}`;
  } else if (eventData.type === "event") {
    // Display the received event data in the events div
    const eventsDiv = document.getElementById("events");
    eventsDiv.innerHTML += `<p>${JSON.stringify(eventData.data)}</p>`;
  }
};

// Event listener when the WebSocket connection is closed
socket.onclose = function (event) {
  console.log("WebSocket connection closed");
};
