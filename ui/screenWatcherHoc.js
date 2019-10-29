import React from 'react';
import _isObject from 'lodash/isObject';

import {store} from 'components';
import {setWidth, setMedia} from '../actions/screen';

export default (media) => WrappedComponent => class ScreenWatcherHoc extends React.PureComponent {

    static WrappedComponent = WrappedComponent;

    /**
     * Proxy real name, prop types and default props for storybook
     */
    static displayName = WrappedComponent.displayName || WrappedComponent.name;

    static _onResize() {
        store.dispatch(setWidth(window.innerWidth));
    }

    componentWillMount() {
        if (typeof window !== 'undefined') {
            store.dispatch(setWidth(window.innerWidth, true));
            if (_isObject(media)) {
                store.dispatch(setMedia(media));
            }
        }
    }

    componentDidMount() {
        if (typeof window !== 'undefined') {
            window.addEventListener('resize', ScreenWatcherHoc._onResize, false);
        }
    }

    componentWillUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('resize', ScreenWatcherHoc._onResize);
        }
    }

    render() {
        return (
            <WrappedComponent {...this.props}/>
        );
    }

};
