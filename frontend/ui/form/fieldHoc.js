import React from 'react';

export default config => WrappedComponent => class FieldHOC extends React.PureComponent {

    static WrappedComponent = WrappedComponent;

    constructor() {
        super(...arguments);

        this.state = {
            data: null,
        };

        this._fetch = this._fetch.bind(this);
    }

    componentDidMount() {
        this._fetch();
    }

    render() {
        return (
            <WrappedComponent
                {...this.props}
            />
        );
    }

};