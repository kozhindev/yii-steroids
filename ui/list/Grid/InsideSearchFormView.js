import React from 'react';

export default class InsideSearchFormView extends React.Component {

    render() {
        return (
            <tr>
                {this.props.children}
            </tr>
        );
    }

}
