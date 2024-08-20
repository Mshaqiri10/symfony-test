import React from 'react';
import ReactDOM from 'react-dom';
import './styles/app.css';
import Header from './Header'; // Ensure the correct path

function App() {
    return (
        <div>
            <Header />
            <h1>Hello, React!</h1>
        </div>
    );
}

ReactDOM.render(<App />, document.getElementById('root'));
