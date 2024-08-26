import express from 'express';
import path from 'path';
const app = express();

app.use(express.static(path.join(import.meta.dirname, 'public')));

app.set('view engine', 'ejs');

app.get('/', (req, res) => {
    res.render('index',
        {
            title: 'Login page',
            msg: '',
            scripts: [
                'js/script.js'
            ],
            styles: [
                'css/style.css'
            ]
        })
});
app.get('/customers', (req, res) => {
    res.render('customers/list',
        {
            title: 'Home page',
            msg: 'Welcome to about page',
            scripts: [
                'https://cdn.jsdelivr.net/npm/bootstrap-table/dist/bootstrap-table.min.js',
                'js/script.js'
            ],
            styles: [
                'css/style.css'
            ]
        })
});


const PORT = process.env.FRONTEND_PORT || 3001;
app.listen(PORT, () => console.log(`Server running on port ${PORT}`));