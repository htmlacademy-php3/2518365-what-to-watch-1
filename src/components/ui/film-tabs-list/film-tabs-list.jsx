import React from 'react';
import { FilmTabsNames } from '../../../const';
import PropTypes from 'prop-types';

function FilmTabsList(props) {
  const {activeTab, tabClickHandler} = props;

  return (
    <ul className="film-nav__list">
      {
        Object.keys(FilmTabsNames).map((button) => {
          const currentTab = FilmTabsNames[button];

          return (
            <li
              key={currentTab}
              className={`film-nav__item ${currentTab === activeTab ? 'film-nav__item--active' : ''}`}
            >
              <a
                href="/"
                className="film-nav__link"
                onClick={(evt) => {
                  evt.preventDefault();
                  tabClickHandler(currentTab);
                }}
              >
                {currentTab}
              </a>
            </li>
          );
        })
      }
    </ul>
  );
}

FilmTabsList.propTypes = {
  activeTab: PropTypes.string.isRequired,
  tabClickHandler: PropTypes.func.isRequired,
};

export default FilmTabsList;
