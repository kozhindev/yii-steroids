import React from 'react';
import ReactDOM from 'react-dom';
import {hot} from 'react-hot-loader'

class MyComponent extends React.Component {
    render() {
        return (
            <div>
                Test
            </div>
        );
    }
}

const HotComponent = hot(module)(MyComponent);

setTimeout(() => {
    ReactDOM.render(
        <HotComponent/>,
        document.body.querySelector('div')
    );
}, 10);