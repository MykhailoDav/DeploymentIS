const express = require("express");
const app = express();
app.get("/", (req, res) => {
    res.send("<h1>Hello from my custom Node.js Docker image!</h1>");
});
app.get("/about", (req, res) => {
    res.send("<h2>This is a simple Express app running in Docker</h2>");
});
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});