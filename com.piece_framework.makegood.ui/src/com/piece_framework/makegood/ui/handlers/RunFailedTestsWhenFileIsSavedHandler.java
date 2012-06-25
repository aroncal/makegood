/**
 * Copyright (c) 2012 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of MakeGood.
 *
 * This program and the accompanying materials are made available under
 * the terms of the Eclipse Public License v1.0 which accompanies this
 * distribution, and is available at http://www.eclipse.org/legal/epl-v10.html
 */

package com.piece_framework.makegood.ui.handlers;

import com.piece_framework.makegood.ui.actions.RunFailedTestsWhenFileIsSavedAction;

/**
 * @sicne 2.1.0
 */
public class RunFailedTestsWhenFileIsSavedHandler extends ToggleHandler {
    @Override
    protected String getActionId() {
        return RunFailedTestsWhenFileIsSavedAction.ACTION_ID;
    }
}
